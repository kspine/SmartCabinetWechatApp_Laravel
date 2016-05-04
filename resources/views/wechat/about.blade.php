<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>配电物联网</title>
    <link rel="stylesheet" href="http://cdn.sg-z.com/css/weui.min.css" type="text/css"/>
    <link rel="stylesheet" href="http://cdn.sg-z.com/css/iotsgcc-min.css" type="text/css">
</head>
<body>

<div class="page article">
    <div class="hd">
        <h1 class="page_title">智能钥匙管理箱系统</h1>
    </div>
    <div class="bd">
        <article class="weui_article">
            <h1>1 项目简介</h1>
            <section>
                <section>
                    <h3>1.1 背景说明</h3>
                    <p>智能钥匙管理箱系统，是公司配电专业的科技创新项目之一。其主要利用系统硬件设备灵活完善的物联网功能，并结合公众号平台的方便快捷，解决了之前由于全市开闭所钥匙及使用者都众多而难于追踪和管理的问题。</p>
                </section>
                <section>
                    <h3>1.2 设备介绍</h3>
                    <p>本智能钥匙箱是通过对普通数字密码保密柜的改造而成，添加了WIFI模块、指纹模块、钥匙检测矩阵、RTC时钟电路等。智能系统可采用内置6节AAA电池工作（正常3个月），也可采用外置7-12V DC电源供电。原数字密码锁采用的是独立电池供电不受智能系统电源影响。</p>
                    <br>
                    <p>钥匙箱门板外部示意图：</p>
                    <img src="http://cdn.sg-z.com/assets/device/front.jpg" width="100%">
                    <br>
                    <p>钥匙箱门板内侧示意图：</p>
                    <img src="http://cdn.sg-z.com/assets/device/back.jpg" width="100%">
                    <br>
                    <p>钥匙检测矩阵示意图：</p>
                    <img src="http://cdn.sg-z.com/assets/device/matrix.jpg" width="100%">
                </section>
            </section>
            <h1>2 系统基本技术构架</h1>
            <section>
                <p>在电池供电工作模式下，智能钥匙管理箱硬件系统为了最大方式节电，主控芯片ATMega2560以Power Down方式一直处于睡眠并且关闭了除指纹模块以外其他所有模块（RTC,WIFI等等）。此时指纹模块也处于睡眠状态，直到检测到有用户手指触摸指纹面板，指纹模块即刻醒来并且给主控芯片发送一个IRQ脉冲将其唤醒。主控被唤醒后向指纹模块发送验证指纹命令，如不通过则返回到睡眠模式，如用户通过验证则记录其指纹ID并驱动电机开锁，扫描此时钥匙矩阵得出开门前所有钥匙状态，继续睡眠。用户锁门后主控会再次被中断唤醒，此时再次扫描钥匙矩阵得出关门后所有钥匙状态，打开RTC模块读取时间，再打开WIFI模块向服务器传送用户指纹ID，操作时间、开门前后两次钥匙矩阵状态的加密数据，若传送失败则将数据储存到EEPROM下次再发。最后关闭RTC、WIFI等模块并向指纹模块发送睡眠指令，自己睡眠，一次完整流程结束。</p>
                <img src="http://cdn.sg-z.com/assets/device/architecture.jpg" width="100%">
                <p>系统基本构架如上图所示。特别注意的是当用户点击公众号功能菜单时会通过微信内置游览器请求配电专业服务器相应的web页面，当改页面需要判断用户权限时会直接引导其进行静默的OAuth2.0, 这也是基于微信公众平台的优势之一，即无需用户输入用户名和密码便可以判断其身份以便向其开放不同的系统权限和功能。</p>
            </section>
            <h1>3 公众号使用说明</h1>
            <section>
                <section id="search">
                    <h3>3.1 钥匙搜索规则</h3>
                    <p>用户可通过向公众号发送语音或者输入文字进行钥匙检索。本公众号实现了简单的智能语义理解，例如用户可直接说出钥匙对应的开闭所名字或者“XX号钥匙”、“全部可借钥匙”、“所有借出钥匙”等，公众号均能理解并准确回复用户希望检索到的钥匙。</p>
                </section>
                <section>
                    <h3>3.2 功能权限</h3>
                    <p>本公众号部分功能需要一定操作权限，请与管理员联系或点击技术咨询菜单了解详情。</p>
                </section>
            </section>
            <h1>4 设备使用说明</h1>
            <section>
                <section>
                    <h3>4.1 开门方式</h3>
                    <p>本钥匙箱可采用三种开锁方式：指纹开锁，数字密码开锁，机械钥匙开锁。在未录入指纹或遗忘密码的情况下，请扣开门把手下方的钥匙孔插入钥匙开锁（仅限紧急情况使用）。</p>
                </section>
                <section>
                    <h3>4.2 指纹开锁</h3>
                    <p>用户在指纹识别区放上手指后，“开门锁门”信号灯闪鸣一声表示验证通过，电机锁打开，请在2秒内转动门把手开门。若出现两次急促闪鸣告警则表示指纹识别未通过，请重试（干燥手指拒认率较高，可在开门前对手指哈气湿润手指以提高识别率）。尤其注意，过多验证失败可能导致指纹模块自锁，请空转把手一两次重新激活她。</p>
                </section>
                <section>
                    <h3>4.3 钥匙取放</h3>
                    <p>取放钥匙请务必根据每把钥匙标牌将其对号入座，还钥匙时请确保钥匙插好。</p>
                </section>
                <section>
                    <h3>4.4 关于锁门</h3>
                    <p>取放钥匙后请务必将把手转至水平位置锁门。开门2分钟后如果箱门未锁，系统会发出告警声提示用户锁门。</p>
                </section>
            </section>
            <h1>5 设备配置说明</h1>
            <section>
                <section>
                    <h3>5.1 配置模式</h3>
                    <p>箱门内侧红色按钮为系统重启键，重启后设备首先会进行硬件自检：正常情况下三盏信号灯依次鸣亮一次，电机锁开动一次。此后4秒内如果用户触摸指纹面板一次则设备进入网络配置模式，触摸两次则进入指纹管理模式，三次则会清空之前网络参数强制重新配网。</p>
                </section>
                <section>
                    <h3>5.2 WIFI配置</h3>
                    <p>按照步骤一进入网络配置模式并打开微信公众号的“设备配置”->“钥匙箱WIFI配置”选项，当AirKiss指示灯急闪后请点击开始配置按钮，向设备传入无线网的SSID和密码。</p>
                </section>
                <section>
                    <h3>5.3 指纹管理</h3>
                    <p> 指纹删录以及系统时间设置都是需要手机先于设备建立连接，由于微信未开放局域网UDP通信API，因此管理人员需要自行下载UDP通信APP，推荐TCP/UDP Terminal。关于配置密码和命令行格式请联系技术咨询。</p>
                </section>
                <section>
                    <h3>5.4 配置结束</h3>
                    <p>开机后4秒内若用户未触摸指纹面板以及配置结束后系统都会正常进入工作模式。</p>
                </section>
            </section>
        </article>
    </div>
</div>
</body>
</html>