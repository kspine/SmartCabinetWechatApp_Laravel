<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class MailAdmin extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $client;
    public $msg;
    public function __construct($client,$event_msg)
    {
        $this->client = $client;
        $this->msg = $event_msg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send('wechat.emails.notify',['client'=>$this->client,'msg'=>$this->msg],
                function ($message){
                    $message->to('admin@sg-z.com','Hao Xiong')->subject('宣城配电物联网公众号');
                });
    }
}
