# NagiosDashBoard
NagiosDashBoard-Nagios监控展示页面

需要ndoutils的支持

1、配置merlin.php文件

  首先修改13行的用户名密码 root是连接数据的用户名，123.com是连接数据的密码，修改成自己的
  
      $con = mysql_connect("localhost", "root", "123.com") or die("<h3><font color=red>不能连接到数据库!</font></h3>");

  
  修改19行的数据，nagiosndo 你的NDO数据保存的数据库名
  
      $db = mysql_select_db("nagiosndo", $con);
 
2、修改index.php文件
    
    $refreshvalue = 30;  //页面刷新时间秒
    
    $pagetitle = "Nagios-监控展示页面";     //页面title显示类容
    
    
    
3、修改js/nagios.js
 
    refreshValue = 30;    //页面刷新时间设定
    
    watermark({watermark_txt:'背景水印文字' + " "+getNowFormatDate()});
    
    
效果

![image](https://github.com/dcl-lily/NagiosDashBoard/raw/master/demo.jpg)

