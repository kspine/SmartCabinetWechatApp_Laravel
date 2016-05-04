<?php

namespace App\Http\Controllers;

use App\Client;
use App\Events\SmartkeyBackEvent;
use App\History;
use App\Smartkey;
use Illuminate\Http\Request;
use App\Record;
use Exception;
use Illuminate\Support\Facades\Event;

class RecordController extends Controller
{
    //
    const SUCCESS = 'Motionlife-OK';
    protected $outs = [];
    protected $ins = [];

    public function processRecord(Request $request)
    {
        $finger_id = $request->input('finger_id', 0);
        $acted_at = date('Y-m-d H:i:s', $request->input('acted_at')-3600*8);
        //the type of this array elements is string
        //$lastRecord = Record::select('state_a','state_b','state_c','state_d')->orderBy('id','DESC')->first();
        //$plast = [$lastRecord->state_a,$lastRecord->state_b,$lastRecord->state_c,$lastRecord->state_d,];
        //the data type of each element is float with no decimal point
        $last = [$request->input('a0'), $request->input('b0'), $request->input('c0'), $request->input('d0'),];
        $present = [$request->input('a1'), $request->input('b1'), $request->input('c1'), $request->input('d1'),];

        //1.caculate which key's state has been changed, then update it
        $this->updateSate($last, $present, $finger_id, $acted_at);
        //2 write into history
        $this->writeHistory($finger_id, $acted_at);
        //3.save the latest states to my database
        Record::create(['finger_id' => $finger_id, 'state_a' => $present[0], 'state_b' => $present[1], 'state_c' => $present[2], 'state_d' => $present[3], 'acted_at' => $acted_at]);

        header('Connection: close');
        header_remove('X-Powered-By');
        return RecordController::SUCCESS;
    }

    private function updateSate(array $last, array $present, $finger_id, $inout_time)
    {
        for ($k = 0; $k < 4; $k++) {
            $xor = (float)$last[$k] ^ (float)$present[$k];
            for ($i = 0; $i < 32; $i++) {
                $sn = 32 * ($k + 1) - $i;
                //in keypad high level (1) means key exsits low level 0 means key is not there
                $newState = (float)$present[$k] & (1<<$i)?0:1;
                //update (32-$i)th smartkey based on $a & (1<<$i)
                Smartkey::where('sn', $sn)->update(['state' => $newState]);

                //if there is a discrepency between the two states we fire key state change event
                if ($xor & (1 << $i))
                {
                    $this->stateChangeEvent($sn, $newState);
                    Smartkey::where('sn', $sn)->update(['finger_id' => $finger_id, 'subscribers' => '', 'inout_time' => $inout_time]);
                }
            }
        }
    }

    //when user put key back do some logic here make sure to return 0;
    private function stateChangeEvent($sn, $newState)
    {
        if ($newState==0) {
            try {
                $key = Smartkey::select('door', 'subscribers')->where('sn', $sn)->first();
                if ($key->subscribers) {
                    Event::fire(new SmartkeyBackEvent($key->subscribers, $sn, $key->door));
                }
            } catch (Exception $e) {
            }
            array_push($this->ins,$sn);

        }else{

            array_push($this->outs, $sn);
        }
    }

    private function writeHistory($finger_id, $acted_at)
    {
        try {
            $cid = Client::where('finger_id', $finger_id)->value('id');
            if (!empty($this->ins)) History::create(['client_id' => $cid, 'action' => '0', 'acted_at' => $acted_at, 'keysns' => implode(':', $this->ins)]);
            if (!empty($this->outs)) History::create(['client_id' => $cid, 'action' => '1', 'acted_at' => $acted_at, 'keysns' => implode(':', $this->outs)]);
        } catch (Exception $e) {
            //TODO what if a client who hasn's register took the key
        }
    }
}
