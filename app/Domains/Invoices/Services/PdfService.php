<?php

namespace App\Domains\Invoices\Services;

use App\Models\Invoice;
use FPDF;

class PdfService
{
  public function execute(Invoice $invoice)
  {
    $invoice->load('items.product');

    $isInvoice = $this->isInvoice($invoice);
    $documentTitle = $isInvoice ? 'INVOICE' : 'PROFORMA INVOICE';
    $documentNumber = $isInvoice ? $invoice->invoice_number : $invoice->proforma_number;
    $fileName = str_replace(['/', '\\'], '-', $documentNumber) . '.pdf';

    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->SetMargins(12, 12, 12);
    $pdf->AddPage();

    $this->renderHeader($pdf, $invoice, $documentTitle, $documentNumber);
    $this->renderInvoiceTable($pdf, $invoice);
    $this->renderFooter($pdf);

    return response($pdf->Output('S'), 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'inline; filename="' . $fileName . '"',
    ]);
  }

  private function formatPaymentTerms(?string $paymentTerms): string
  {
    return match ($paymentTerms) {
      'cbd' => 'Cash Before Delivery',
      'cod' => 'Cash on Delivery',
      'dp' => 'Down Payment',
      default => '-',
    };
  }

  private function isInvoice(Invoice $invoice): bool
  {
    return !empty($invoice->invoice_number);
  }

  private function documentDate(Invoice $invoice)
  {
    if (!$this->isInvoice($invoice)) {
      return $invoice->issued_date;
    }

    return optional($invoice->payments()->latest('payment_date')->first())->payment_date;
  }

  private function renderHeader(FPDF $pdf, Invoice $invoice, string $documentTitle, string $documentNumber): void
  {
    $left = 11;
    $right = 200;
    $logoPath = public_path('assets/img/branding/logo-dna.png');

    if (file_exists($logoPath)) {
      $pdf->Image($logoPath, $left, 14, 26);
    }

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetXY(98, 18);
    $pdf->Cell($right - 98, 7, 'DNA LIGHTING', 0, 1, 'R');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetX(98);
    $pdf->Cell($right - 98, 6, 'Spesialis APILL, Rambu, dan Tiang', 0, 1, 'R');

    $pdf->SetX(98);
    $pdf->Cell($right - 98, 6, '0852 1000 1116', 0, 1, 'R');

    $pdf->SetFont('Arial', '', 8);

    $pdf->SetX(98);
    $pdf->Cell($right - 98, 4, 'Grand Village Sepatan Blok I01 No 04', 0, 1, 'R');

    $pdf->SetX(98);
    $pdf->Cell($right - 98, 4, 'Jl Raya Mauk, Kec. Sepatan, Kab. Tangerang', 0, 1, 'R');

    $pdf->SetY(56);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, $documentTitle, 0, 1, 'C');

    $startY = 72;

    $pdf->SetFont('Arial', '', 8);

    $pdf->SetXY($left, $startY);
    $pdf->Cell(0, 4, 'Invoice No: ' . $documentNumber, 0, 1, 'L');

    $pdf->SetX($left);
    $pdf->Cell(0, 4, 'Ref: ' . ($invoice->reference ?: '-'), 0, 1, 'L');

    $pdf->SetX($left);
    $pdf->Cell(0, 4, 'Payment Terms: ' . $this->formatPaymentTerms($invoice->payment_terms), 0, 1, 'L');

    $buyerX = 120;
    $labelW = 18;
    $colonW = 3;

    $pdf->SetFont('Arial', '', 8);

    $pdf->SetXY($buyerX, $startY);
    $pdf->Cell($labelW, 4, 'Name', 0, 0);
    $pdf->Cell($colonW, 4, ':', 0, 0);
    $pdf->Cell(55, 4, $invoice->customer_name, 0, 1);

    $pdf->SetXY($buyerX, $startY + 4);
    $pdf->Cell($labelW, 4, 'Address', 0, 0);
    $pdf->Cell($colonW, 4, ':', 0, 0);

    $addressX = $buyerX + $labelW + $colonW;
    $addressY = $startY + 4;

    $pdf->SetXY($addressX, $addressY);
    $pdf->MultiCell(
      55,
      4,
      $invoice->customer_address ?: '-',
      0,
      'L'
    );

    $pdf->SetY(max($startY + 12, $pdf->GetY()) + 4);
  }

  private function renderInvoiceTable(FPDF $pdf, Invoice $invoice): void
  {
    $x = 12;
    $y = $pdf->GetY();
    $w = 186;

    $cols = [
      ['label' => 'No', 'w' => 8],
      ['label' => 'Description', 'w' => 80],
      ['label' => 'Qty', 'w' => 8],
      ['label' => 'UoM', 'w' => 10],
      ['label' => 'Unit Price', 'w' => 32],
      ['label' => 'Discount', 'w' => 16],
      ['label' => 'Total', 'w' => 32],
    ];

    // Item header
    $tableY = $y;
    $headerH = 6;
    $rowAreaH = 88;

    $pdf->Rect($x, $tableY, $w, $headerH + $rowAreaH);

    $currentX = $x;
    foreach ($cols as $col) {
      $pdf->Rect($currentX, $tableY, $col['w'], $headerH);
      $pdf->SetFont('Arial', 'B', 8);
      $pdf->SetXY($currentX, $tableY + 1);
      $pdf->Cell($col['w'], 4, $col['label'], 0, 0, 'C');
      $currentX += $col['w'];
    }

    $currentX = $x;
    foreach ($cols as $col) {
      $pdf->Line($currentX, $tableY, $currentX, $tableY + $headerH + $rowAreaH);
      $currentX += $col['w'];
    }
    $pdf->Line($x + $w, $tableY, $x + $w, $tableY + $headerH + $rowAreaH);

    $rowY = $tableY + $headerH + 1;
    $rowH = 4;
    $rowGap = 3;

    foreach ($invoice->items as $index => $item) {
      $productName = $item->product->name ?? $item->product->product_name ?? '-';

      $descLines = $this->countWrappedLines($pdf, $productName, $cols[1]['w'] - 2);
      $actualRowH = max($rowH, $descLines * $rowH);

      $pdf->SetFont('Arial', '', 8);

      $currentX = $x;

      $pdf->SetXY($currentX, $rowY);
      $pdf->Cell($cols[0]['w'], $rowH, $index + 1, 0, 0, 'C');
      $currentX += $cols[0]['w'];

      $pdf->SetXY($currentX + 1, $rowY);
      $pdf->MultiCell($cols[1]['w'] - 2, $rowH, $productName, 0, 'L');
      $currentX += $cols[1]['w'];

      $pdf->SetXY($currentX, $rowY);
      $pdf->Cell($cols[2]['w'], $rowH, $this->formatNumber($item->quantity), 0, 0, 'C');
      $currentX += $cols[2]['w'];

      $pdf->SetXY($currentX, $rowY);
      $pdf->Cell($cols[3]['w'], $rowH, $item->uom, 0, 0, 'C');
      $currentX += $cols[3]['w'];

      $this->moneyCell($pdf, $currentX, $rowY, $cols[4]['w'], $rowH, $item->unit_price);
      $currentX += $cols[4]['w'];

      $pdf->SetXY($currentX, $rowY);
      $pdf->Cell($cols[5]['w'], $rowH, $this->formatPercent($item->discount) . '%', 0, 0, 'R');
      $currentX += $cols[5]['w'];

      $this->moneyCell($pdf, $currentX, $rowY, $cols[6]['w'], $rowH, $item->line_total);

      $rowY += $actualRowH + $rowGap;
    }

    // Summary box
    $summaryY = $tableY + $headerH + $rowAreaH;

    if ($this->isInvoice($invoice)) {
      $summaryBottomY = $this->renderSummaryInvoice(
        $pdf,
        $invoice,
        $x,
        $summaryY,
        $w,
        $cols
      );
    } else {
      $summaryBottomY = $this->renderSummaryProforma(
        $pdf,
        $invoice,
        $x,
        $summaryY,
        $w,
        $cols
      );
    }

    // Terbilang
    $terbilangY = $summaryBottomY;
    $terbilangH = 8;

    $pdf->Rect($x, $terbilangY, $w, $terbilangH);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY($x + 0.8, $terbilangY + 1.7);
    $pdf->Cell(18, 4, 'Amount in Words :', 0, 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY($x + 31, $terbilangY + 1.7);
    $pdf->Cell($w - 22, 4, ucfirst(trim($this->terbilang((int) round($invoice->grand_total)))) . ' rupiah', 0, 0);

    // Signature
    $signY = $terbilangY + $terbilangH;
    $signH = 28;

    $pdf->Rect($x, $signY, $w, $signH);

    $signX = 155;
    $signW = 50;

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY($signX, $signY + 1);
    $pdf->Cell($signW, 4, 'Tangerang, ' . optional($this->documentDate($invoice))->format('d F Y'), 0, 1, 'C');

    $pdf->SetXY($signX, $signY + $signH - 5);
    $pdf->Cell($signW, 4, 'Muhamad Nanang Fauzi', 0, 1, 'C');

    // Notes
    $notesY = $signY + $signH + 4;

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY($x, $notesY);
    $pdf->Cell(0, 4, 'Notes:', 0, 1);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetX($x);
    $pdf->MultiCell($w, 4, "Pembayaran ditransfer ke BCA, 0680024413 A/N MUHAMAD NANANG FAUZI\n" . "Dokumen ini dibuat secara elektronik dan mengikat secara hukum tanpa tanda tangan", 0, 'L');
  }

  private function renderSummaryProforma(
    FPDF $pdf,
    Invoice $invoice,
    float $x,
    float $summaryY,
    float $w,
    array $cols
  ): float {
    $summaryH = 15;

    $summaryLeftW = $cols[0]['w'] + $cols[1]['w'];

    $summaryLabelW =
      $cols[2]['w']
      + $cols[3]['w']
      + $cols[4]['w']
      + $cols[5]['w'];

    $summaryValueW = $cols[6]['w'];

    $summaryTotalLineY = 10;

    $pdf->Rect($x, $summaryY, $w, $summaryH);

    $pdf->Line(
      $x + $summaryLeftW,
      $summaryY,
      $x + $summaryLeftW,
      $summaryY + $summaryH
    );

    $pdf->Line(
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + $summaryH
    );

    $pdf->Line(
      $x + $summaryLeftW,
      $summaryY + $summaryTotalLineY,
      $x + $w,
      $summaryY + $summaryTotalLineY
    );

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY($x + $summaryLeftW + 0.8, $summaryY + 0.7);
    $pdf->Cell($summaryLabelW - 1.6, 3, 'SUBTOTAL');

    $pdf->SetFont('Arial', '', 8);
    $this->moneyCell(
      $pdf,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + 0.7,
      $summaryValueW,
      3,
      $invoice->subtotal
    );

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY($x + $summaryLeftW + 0.8, $summaryY + 6.4);
    $pdf->Cell($summaryLabelW - 1.6, 3, 'DISCOUNT');

    $pdf->SetFont('Arial', '', 8);
    $this->moneyCell(
      $pdf,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + 6.4,
      $summaryValueW,
      3,
      $invoice->discount_total
    );

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY($x + $summaryLeftW + 0.8, $summaryY + 10.6);
    $pdf->Cell($summaryLabelW - 1.6, 3.5, 'TOTAL');

    $this->moneyCell(
      $pdf,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + 10.6,
      $summaryValueW,
      3.5,
      $invoice->grand_total
    );

    return $summaryY + $summaryH;
  }

  private function renderSummaryInvoice(
    FPDF $pdf,
    Invoice $invoice,
    float $x,
    float $summaryY,
    float $w,
    array $cols
  ): float {
    $summaryH = 25;

    $summaryLeftW = $cols[0]['w'] + $cols[1]['w'];

    $summaryLabelW =
      $cols[2]['w']
      + $cols[3]['w']
      + $cols[4]['w']
      + $cols[5]['w'];

    $summaryValueW = $cols[6]['w'];

    $summaryTotalLineY = 10;

    $pdf->Rect($x, $summaryY, $w, $summaryH);

    $pdf->Line(
      $x + $summaryLeftW,
      $summaryY,
      $x + $summaryLeftW,
      $summaryY + $summaryH
    );

    $pdf->Line(
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + $summaryH
    );

    $pdf->Line(
      $x + $summaryLeftW,
      $summaryY + $summaryTotalLineY,
      $x + $w,
      $summaryY + $summaryTotalLineY
    );

    $pdf->Line(
      $x + $summaryLeftW,
      $summaryY + 15,
      $x + $w,
      $summaryY + 15
    );

    $pdf->Line(
      $x + $summaryLeftW,
      $summaryY + 20,
      $x + $w,
      $summaryY + 20
    );

    $pdf->SetFont('Arial', '', 8);

    $pdf->SetXY(
      $x + $summaryLeftW + 0.8,
      $summaryY + 0.7
    );

    $pdf->Cell(
      $summaryLabelW - 1.6,
      3,
      'SUBTOTAL'
    );

    $pdf->SetFont('Arial', '', 8);

    $this->moneyCell(
      $pdf,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + 0.7,
      $summaryValueW,
      3,
      $invoice->subtotal
    );

    $pdf->SetFont('Arial', '', 8);

    $pdf->SetXY(
      $x + $summaryLeftW + 0.8,
      $summaryY + 6.4
    );

    $pdf->Cell(
      $summaryLabelW - 1.6,
      3,
      'DISCOUNT'
    );

    $pdf->SetFont('Arial', '', 8);

    $this->moneyCell(
      $pdf,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + 6.4,
      $summaryValueW,
      3,
      $invoice->discount_total
    );

    $pdf->SetFont('Arial', 'B', 8);

    $pdf->SetXY(
      $x + $summaryLeftW + 0.8,
      $summaryY + 10.6
    );

    $pdf->Cell(
      $summaryLabelW - 1.6,
      3.5,
      'TOTAL'
    );

    $this->moneyCell(
      $pdf,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + 10.6,
      $summaryValueW,
      3.5,
      $invoice->grand_total
    );

    $pdf->SetFont('Arial', '', 8);

    $pdf->SetXY(
      $x + $summaryLeftW + 0.8,
      $summaryY + 15.8
    );

    $pdf->Cell(
      $summaryLabelW - 1.6,
      3,
      'AMOUNT PAID'
    );

    $this->moneyCell(
      $pdf,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + 15.8,
      $summaryValueW,
      3,
      $invoice->paid_amount
    );

    $pdf->SetFont('Arial', 'B', 8);

    $pdf->SetXY(
      $x + $summaryLeftW + 0.8,
      $summaryY + 20.8
    );

    $pdf->Cell(
      $summaryLabelW - 1.6,
      3,
      'BALANCE DUE'
    );

    $this->moneyCell(
      $pdf,
      $x + $summaryLeftW + $summaryLabelW,
      $summaryY + 20.8,
      $summaryValueW,
      3,
      $invoice->remaining_amount
    );

    return $summaryY + $summaryH;
  }

  private function renderFooter(FPDF $pdf): void
  {
    $pdf->SetAutoPageBreak(false);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(12, 290);
    $pdf->Cell(0, 4, 'https://dna-lighting.com', 0, 0, 'L');
  }

  private function countWrappedLines(FPDF $pdf, string $text, float $width): int
  {
    $words = explode(' ', $text);
    $lines = 1;
    $currentLine = '';

    foreach ($words as $word) {
      $testLine = trim($currentLine . ' ' . $word);

      if ($pdf->GetStringWidth($testLine) > $width) {
        $lines++;
        $currentLine = $word;
      } else {
        $currentLine = $testLine;
      }
    }

    return $lines;
  }

  private function moneyCell(FPDF $pdf, float $x, float $y, float $w, float $h, $value): void
  {
    $pdf->SetXY($x + 1, $y);
    $pdf->Cell(6, $h, 'Rp', 0, 0, 'L');

    $pdf->SetXY($x, $y);
    $pdf->Cell($w - 1, $h, $this->moneyPlain($value), 0, 0, 'R');
  }

  private function moneyPlain($value): string
  {
    return number_format((float) $value, 0, ',', '.');
  }

  private function formatNumber($value): string
  {
    return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
  }

  private function formatPercent($value): string
  {
    return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
  }

  private function limitText(string $text, int $limit): string
  {
    return strlen($text) > $limit ? substr($text, 0, $limit - 3) . '...' : $text;
  }

  private function terbilang(int $value): string
  {
    $words = [
      '',
      'satu',
      'dua',
      'tiga',
      'empat',
      'lima',
      'enam',
      'tujuh',
      'delapan',
      'sembilan',
      'sepuluh',
      'sebelas'
    ];

    if ($value < 12) {
      return $words[$value];
    }

    if ($value < 20) {
      return $this->joinWords($this->terbilang($value - 10), 'belas');
    }

    if ($value < 100) {
      return $this->joinWords(
        $this->terbilang((int) ($value / 10)),
        'puluh',
        $this->terbilang($value % 10)
      );
    }

    if ($value < 200) {
      return $this->joinWords('seratus', $this->terbilang($value - 100));
    }

    if ($value < 1000) {
      return $this->joinWords(
        $this->terbilang((int) ($value / 100)),
        'ratus',
        $this->terbilang($value % 100)
      );
    }

    if ($value < 2000) {
      return $this->joinWords('seribu', $this->terbilang($value - 1000));
    }

    if ($value < 1000000) {
      return $this->joinWords(
        $this->terbilang((int) ($value / 1000)),
        'ribu',
        $this->terbilang($value % 1000)
      );
    }

    if ($value < 1000000000) {
      return $this->joinWords(
        $this->terbilang((int) ($value / 1000000)),
        'juta',
        $this->terbilang($value % 1000000)
      );
    }

    if ($value < 1000000000000) {
      return $this->joinWords(
        $this->terbilang((int) ($value / 1000000000)),
        'milyar',
        $this->terbilang($value % 1000000000)
      );
    }

    return $this->joinWords(
      $this->terbilang((int) ($value / 1000000000000)),
      'triliun',
      $this->terbilang($value % 1000000000000)
    );
  }

  private function joinWords(string ...$words): string
  {
    return trim(preg_replace('/\s+/', ' ', implode(' ', array_filter($words))));
  }
}
