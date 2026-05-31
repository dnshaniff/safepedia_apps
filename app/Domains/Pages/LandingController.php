<?php

namespace App\Domains\Pages;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Brand;
use App\Models\Product;

class LandingController extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'front'];

    $products = Product::query()->with('thumbnail')->where('status', 'active')->latest()->take(4)->get();

    $articles = Article::query()->with('thumbnail')->where('status', 'published')->latest()->take(4)->get();

    $brands = Brand::query()->latest()->get();

    return view('content.landing.index', compact('pageConfigs', 'products', 'articles', 'brands'));
  }

  public function products()
  {
    $pageConfigs = ['myLayout' => 'front'];

    $products = Product::query()->with('thumbnail')->where('status', 'active')->latest()->get();

    return view('content.landing.products.index', compact('pageConfigs', 'products'));
  }

  public function product(string $slug)
  {
    $pageConfigs = ['myLayout' => 'front'];

    $product = Product::query()->with(['thumbnail', 'images', 'brand'])
      ->where('status', 'active')
      ->where('slug', $slug)
      ->firstOrFail();

    $galleryImages = $product->images->reject(function ($image) use ($product) {
      return $product->thumbnail
        && $image->id === $product->thumbnail->id;
    })->values();

    $relatedProducts = Product::query()
      ->with('thumbnail')
      ->where('status', 'active')
      ->where('id', '!=', $product->id)
      ->latest()
      ->take(4)
      ->get();

    return view('content.landing.products.show', compact('pageConfigs', 'product', 'galleryImages', 'relatedProducts'));
  }

  public function projects()
  {
    $pageConfigs = ['myLayout' => 'front'];

    $projects = Article::query()->with('thumbnail')->where('status', 'published')->latest()->get();

    return view('content.landing.projects.index', compact('pageConfigs', 'projects'));
  }

  public function project(string $slug)
  {
    $pageConfigs = ['myLayout' => 'front'];

    $project = Article::query()
      ->with(['thumbnail', 'images'])
      ->where('slug', $slug)
      ->where('status', 'published')
      ->firstOrFail();

    $galleryImages = $project->images->reject(function ($image) use ($project) {
      return $project->thumbnail
        && $image->id === $project->thumbnail->id;
    })->values();

    $relatedProjects = Article::query()
      ->with('thumbnail')
      ->where('status', 'published')
      ->where('id', '!=', $project->id)
      ->latest()
      ->take(3)
      ->get();

    return view('content.landing.projects.show', compact('pageConfigs', 'project', 'galleryImages', 'relatedProjects'));
  }
}
