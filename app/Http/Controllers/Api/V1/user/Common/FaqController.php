<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\Faq;

class FaqController extends Controller
{
    public function getFaqs()
    {
        $faqs = Faq::where('type', 'for_users')->orWhere('type','all')->select(['question', 'answer'])->latest()->get();

        return jsonResponseData(true, $faqs);
    }
}
