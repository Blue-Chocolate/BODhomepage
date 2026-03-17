<?php

namespace App\Observers;

use App\Mail\ContactUsReplyMail;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Mail;

class ContactUsObserver
{
    public function updating(ContactUs $contactUs): void
    {
        // Only send mail if reply field is being changed and wasn't set before
        $replyChanged = $contactUs->isDirty('reply')
            && !empty($contactUs->reply)
            && empty($contactUs->getOriginal('reply'));

        if ($replyChanged) {
            Mail::to($contactUs->email)->queue(
                new ContactUsReplyMail($contactUs)
            );
        }
    }
}