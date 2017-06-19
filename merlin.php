<?php
#####################################
##### Forked Nagios Dashboard SP ####
#####################################
# SP_V1.0 2010.09.13 first release by Shao-Pin,Cheng.  
# SP_V1.1 1.Support NDOUtils or Merlin. 2.Add aleart sound. 
# 接着大神修改，优化部分逻辑设定设定，修改为中文支持
# 首先修改13行的用户名密码 root是连接数据的用户名，123.com是连接数据的密码，修改成自己的
# 修改19行的数据，nagiosndo 你的NDO数据保存的数据库名
# Contact with me http://www.nagios-portal.org/wbb/index.php?page=User&userID=7931

##### Put the correct hostname    DBuser       DBpasswd #####
$con = mysql_connect("localhost", "root", "123.com") or die("<h3><font color=red>不能连接到数据库!</font></h3>");

##### DB Type 1=NDOUtils 2=Merlin #####
$dbtype=1;

##### DBname #####
$db = mysql_select_db("nagiosndo", $con);

##### Alert Sound On 0=Off 1=On #### 

$SoundOn=1;

function chinesesubstr($str,$start,$len){
        $strlen = $len - $start; 
		$tmpstr='';
        for($i=0;$i<$strlen;$i++){                 
            if(ord(substr($str,$i,1))>0xa0){    
                $tmpstr.=substr($str,$i,3);        
                $i+=2;
            }else{
                $tmpstr.=substr($str,$i,1);
            }
        }
        return $tmpstr;
}


?>
<div class="dash_unhandled hosts dash">
    <h2>主机问题</h2>
    <div class="dash_wrapper">
        <table class="dash_table">
            <?php
            #ALL down-hosts
            if ($dbtype == 1) {
               $query = "select display_name, alias, count(display_name) from nagios_hosts,nagios_hoststatus where nagios_hosts.host_object_id=nagios_hoststatus.host_object_id and current_state = 1 and problem_has_been_acknowledged = 0 group by hoststatus_id";
            } elseif ($dbtype == 2 ) {
              $query = "select host_name, alias, count(host_name) from host where last_hard_state = 1 and problem_has_been_acknowledged = 0 group by host_name";
            }
            $result = mysql_query($query);
            $save = "";
            $output = "";
            while ($row = mysql_fetch_array($result)) {
                $output .=  "<tr class=\"critical\"><td>".$row[0]."</td><td>".$row[1]."</td></tr>";
                $save .= $row[0];
            }
            if($save){
            ?>
            <tr class="dash_table_head">
                <th>主机名</th>
                <th>别名</th>
            </tr>
            <?php 
		print $output;
            }
	    else{ 
                print "<tr class=\"ok\"><td>所有主机正常或者问题已被确认.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
<div class="dash_tactical_overview tactical_overview hosts dash">
    <h2>摘要信息</h2>
    <div class="dash_wrapper">
        <table class="dash_table">
            <tr class="dash_table_head">
                <th>类型</th>
                <th>数量/总计</th>
                <th>百分比</th>
            </tr>
            <?php 
            # number of hosts down
            if ($dbtype == 1) {
              $query = "select count(1) as count from nagios_hoststatus where current_state <> 0 and problem_has_been_acknowledged = 0";
            } elseif ($dbtype == 2 ) {
              $query = "select count(1) as count from host where last_hard_state = 1";
            }	
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $hosts_down = $row[0];
            
            # total number of hosts
            if ($dbtype == 1) {
              $query = "select count(1) as count from nagios_hoststatus";
            } elseif ($dbtype == 2 ) {
              $query = "select count(1) as count from host";
            }
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $total_hosts = $row[0];
            
            $hosts_down_pct = round($hosts_down / $total_hosts * 100, 2);
            $hosts_up = $total_hosts - $hosts_down;
            $hosts_up_pct = round($hosts_up / $total_hosts * 100, 2);
            
            #### SERVICES
            #
            if ($dbtype == 1) {
              $query = "select count(1) as count from nagios_servicestatus where current_state <> 0 and current_state <> 4 and problem_has_been_acknowledged = 0";
            } elseif ($dbtype == 2 ) {
              $query = "select count(1) as count from service where last_hard_state = 1";
            }
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $services_down = $row[0];
            
            # total number of hosts
            if ($dbtype == 1) {
              $query = "select count(1) as count from nagios_servicestatus";
            } elseif ($dbtype == 2 ) {
              $query = "select count(1) as count from service";
            }
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            $total_services = $row[0];
            
            $services_down_pct = round($services_down / $total_services * 100, 2);
            $services_up = $total_services - $services_down;
            $services_up_pct = round($services_up / $total_services * 100, 2);
            
            ?>
            <tr class="ok total_hosts_up">
                <td align=center>正常主机</td>
                <td align=center><?php print $hosts_up; ?>/<?php print $total_hosts; ?></td>
                <td align=center><?php print $hosts_up_pct."%"; ?></td>
            </tr>
			<?php
				if ($hosts_down ==0){
					print "<tr class=\"ok total_hosts_up\">";
				}
				else{
					print "<tr class=\"critical total_hosts_down\">";
				}
			?>
                <td align=center>异常主机</td>
                <td align=center><?php print $hosts_down; ?>/<?php print $total_hosts; ?></td>
                <td align=center><?php print $hosts_down_pct."%"; ?></td>
            </tr>
            <tr class="ok total_services_up">
                <td align=center>正常服务</td>
                <td align=center><?php print $services_up; ?>/<?php print $total_services; ?></td>
                <td align=center><?php print $services_up_pct."%"; ?></td>
            </tr>
			<?php
			if ($services_down ==0){
					print  "<tr class=\"ok total_services_up\">";
				}
				else{
					print "<tr class=\"critical total_services_down\">";
				}
					
			?>
                <td align=center>异常服务</td>
                <td align=center><?php print $services_down; ?>/<?php print $total_services; ?></td>
                <td align=center><?php print $services_down_pct."%"; ?></td>
            </tr>
        </table>
    </div>
</div>
<div class="clear"></div>
<div class="dash_unhandled_service_problems hosts dash">
    <h2>等待处理的问题</h2>
    <div class="dash_wrapper">
        <table class="dash_table">
            <tr class="dash_table_head">
                <th>
                    主机
                </th>
                <th>
                    服务
                </th>
                <th>
                    错误内容
                </th>
                <th>
                    状态改变时间
                </th>
                <th>
                    最后一次检查时间
                </th>
            </tr>
            <?php 
            #ALL critical/warning services on hosts not being down
            if ($dbtype == 1) {
              $query = "select nagios_hosts.display_name,nagios_services.display_name,nagios_servicestatus.current_state,nagios_servicestatus.output, nagios_servicestatus.last_hard_state_change,nagios_servicestatus.last_check";
              $query = $query." from nagios_servicestatus,nagios_hoststatus,nagios_services,nagios_hosts where";
              $query = $query." nagios_hosts.host_object_id = nagios_services.host_object_id and";
              $query = $query." nagios_hosts.host_object_id = nagios_hoststatus.host_object_id and";
              $query = $query." nagios_services.service_object_id = nagios_servicestatus.service_object_id and";
              $query = $query." nagios_servicestatus.problem_has_been_acknowledged = 0 and";
              $query = $query." nagios_servicestatus.current_state in (1,2,3) and";
              $query = $query." nagios_hoststatus.current_state not like 1 order by nagios_servicestatus.current_state desc";
            } elseif ($dbtype == 2 ) {
              $query = "select service.host_name,service.service_description,service.last_hard_state,service.output, service.last_hard_state_change,service.last_check ";
              $query = $query." from service,host where ";
              $query = $query." host.host_name = service.host_name and ";
              $query = $query." service.last_hard_state in (1,2) and ";
              $query = $query." service.problem_has_been_acknowledged = 0 and host.problem_has_been_acknowledged = 0 and ";
              $query = $query." host.last_hard_state not like 1 group by service.service_description order by service.last_hard_state";
            }
            $result = mysql_query($query);
            ?>
            <?php
	    $sound = 0;
			$todaytime=time();
            while ($row = mysql_fetch_array($result)) {
                if ($row[2] == 2) {
                    $class = "critical";
                } elseif ($row[2] == 1) {
                    $class = "warning";
                }
				else{
					$class = "unknown";
				}
				if (($todaytime - strtotime($row[4])) < 60){
					$sound=1;
				}
                ?>
                <tr class="<?php print $class; ?>">
                    <td><?php print $row[0]; ?></td>
                    <td><?php print $row[1]; ?></td>
                    <td><?php print "<a  title=\"".$row[3]."\">".chinesesubstr($row[3],0,25)."</a>"; ?></td>
                    <td class="date date_statechange"><?php print date("d-m-Y H:i:s", strtotime($row[4])); ?></td>
                    <td class="date date_lastcheck"><?php print date("d-m-Y H:i:s", strtotime($row[5])); ?></td>
                </tr>
                <?php 
            }
            ?>
        </table>
		
    </div>
</div>
<?php 
	if ($sound == 1 && $SoundOn == 1 ) {	
        echo "<embed src=\"./media/alert.wav\" width=\"10\" height=\"12\" hidden=\"True\" autostart=\"true\">";
    }

?>
</body>
</html>
