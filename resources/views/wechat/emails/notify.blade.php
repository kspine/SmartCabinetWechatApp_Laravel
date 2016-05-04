
<html>
<head>
       <meta charset="UTF-8">
       <title>Job Queue Testing</title>
       <meta name="format-detection" content="telephone=no"/>
</head>
<body>
<div class="pic">
       <h1>"公众号事件通知"</h1>
</div>
<div class="tips">
       <div></div>
       <ul>
              <li>Event:{{$msg}}</li>
              <li>OpenId:{{$client->openid}}</li>
              <li>Nickname:{{$client->nickname}}</li>
              @if($msg=='User Followed')
              <li>Region:{{$client->country.' '.$client->province.' '.$client->city}}</li>
              <li>Picture:<img src="{{substr($client->headimgurl, 0, -1).'132'}}"/></li>
              @endif
       </ul>
</div>
</body>

</html>

