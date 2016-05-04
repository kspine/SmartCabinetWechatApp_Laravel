<?php

namespace App\Jobs;

use App\Client;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use EasyWeChat\Foundation\Application as WeChat;

class RemindHolder extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    const TEMPLATE_ID = '07O91VjWfxPaSN6ncLZLxw5DTZKfy1paFx1jI2REq54';
    public $key;
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $holder = Client::where('finger_id',$this->key->finger_id)->first();
        if($holder->openid)
        {
            $data=['first'=>"您好，您借出的钥匙目前有人急需使用\n",
                'keyword1'=>$holder->name,
                'keyword2'=>'#'.sprintf('%03d',$this->key->sn),
                'keyword3'=>count(array_filter(explode(':',$this->key->subscribers))),
                'remark'=>"\n请您在用完后及时归还，谢谢！"];
            $messageId = (new Wechat(['notice']))['notice']->withTo($holder->openid)
                ->withUrl('http://iot.sg-z.com/smartkey/'.$this->key->id.'?from=reminder')
                ->withColor('#00cc00')
                ->withTemplate(RemindHolder::TEMPLATE_ID)
                ->withData($data)->send();
        }
    }
}
