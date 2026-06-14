<?php

namespace App\Domains\Pages\Services;

use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
  public function execute(User $user): array
  {
    return DB::transaction(function () use ($user) {
      $google2fa = new Google2FA();

      $secret = $google2fa->generateSecretKey();

      $user->update(['google2fa_secret' => $secret]);

      $qrUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->username, $secret);

      $renderer = new ImageRenderer(new RendererStyle(300), new SvgImageBackEnd());

      $writer = new Writer($renderer);

      $qrSvg = $writer->writeString($qrUrl);

      return ['secret' => $secret, 'qr_url' => $qrUrl, 'qr_svg' => $qrSvg];
    });
  }
}
