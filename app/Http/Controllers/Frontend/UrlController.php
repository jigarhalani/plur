<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Url;
use Facades\App\Helpers\UrlHlp;
use Illuminate\Support\Facades\Auth;

class UrlController extends Controller
{
    /**
     * @param string $url_key
     */
    public function view($url_key)
    {
        $url = Url::whereUrlKey($url_key)
                    ->firstOrFail();

        $qrCode = qrCodeGenerator($url->short_url);

        return view('frontend.short', compact('url'), [
            'qrCodeData'   => $qrCode->getContentType(),
            'qrCodeBase64' => $qrCode->generate(),
        ]);
    }

    /**
     * @param string $url_key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate($url_key)
    {
        $url = Url::whereUrlKey($url_key)
                  ->firstOrFail();

        $url_key = UrlHlp::key_generator();

        $replicate = $url->replicate();
        $replicate->user_id = Auth::id();
        $replicate->url_key = $url_key;
        $replicate->is_custom = 0;
        $replicate->clicks = 0;
        $replicate->save();

        return redirect()->route('short_url.stats', $url_key);
    }
}
