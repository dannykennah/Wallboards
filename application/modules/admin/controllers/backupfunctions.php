if ($api=="update_test2") {
			
			require_once '../library/Dialer/Dialer.php';
			$startofday=strtotime(date('Y-m-d')." 00:00:01");
			$endofday=strtotime(date('Y-m-d')." 23:59:59");
		
			//$options['fields']='date,hour,oc_abdn,nc_all,nc_in,nc_out,nc_man,nc_tpt,nc_ans,nc_que';
			//$options['groupby']='date,hour';
			//$options['groupby']='date,aid';
			//$options['range']=$startofday.":".$endofday;
			$agents2 = api_ajax('inbound_activity', $options);
			$agents = api_ajax('overview', $options);
			echo "<pre>";
			print_r($agents);			
			print_r($agents2);			
			echo "</pre>";

		}
		
		if ($api=="update_test4") {
			
			require_once '../library/Dialer/Dialer.php';
			$startofday=strtotime(date('Y-m-d')." 00:00:01");
			$endofday=strtotime(date('Y-m-d')." 23:59:59");
		
			$options['fields']='date,aid,hour,anm,nc_all,nc_in,nc_out,nc_man,sec_talk,sec_wait,sec_wrap'; 
			$options2['fields']='date,nc_all,nc_in,nc_out,nc_man,sec_talk,sec_wait,sec_wrap';
			$options['groupby']='aid,hour';
			$options2['groupby']='date';
			//$options['groupby']='date,aid';
			$options['range']=$startofday.":".$endofday;
			$options2['range']=$startofday.":".$endofday;
			$agents = api_reporting('calls', $options);
			$agents2 = api_reporting('calls', $options2);
			
			$data = $agents2['list'][0];
			
			echo "<pre>";
			print_r($data);			
			echo "</pre>"; 
			
			$testa=gmdate("H:i:s", $data['sec_talk']);
			$testb=gmdate("H:i:s", $data['sec_wait']);
			$testc=gmdate("H:i:s", $data['sec_wrap']);
			echo "
			talk : ".$testa."<br>
			wait : ".$testb."<br> 
			wrap :".$testc; 
			
			echo "<pre>";
			print_r($agents);			
			echo "</pre>";

		}
		
		if ($api=="update_test5") {
			
			require_once '../library/Dialer/Dialer.php';
			$startofday=strtotime(date('Y-m-d')." 00:00:01");
			$endofday=strtotime(date('Y-m-d')." 23:59:59");
		
			$options['fields']='date,aid,anm,nc_all,nc_in,nc_out,nc_man,sec_talk,sec_wait,sec_wrap';
			$options2['fields']='date,nc_all,nc_in,nc_out,nc_man,sec_talk,sec_wait,sec_wrap';
			$options['groupby']='date,aid';
			$options2['groupby']='date';
			//$options['groupby']='date,aid';
			$options['range']=$startofday.":".$endofday;
			$options2['range']=$startofday.":".$endofday;
			$agents = api_reporting('calls', $options);
			$agents2 = api_reporting('calls', $options2);
			
			$data = $agents2['list'][0];
			
			echo "<pre>";
			print_r($data);			
			echo "</pre>"; 
			
			$testa=gmdate("H:i:s", $data['sec_talk']);
			$testb=gmdate("H:i:s", $data['sec_wait']);
			$testc=gmdate("H:i:s", $data['sec_wrap']);
			echo "
			talk : ".$testa."<br>
			wait : ".$testb."<br> 
			wrap :".$testc; 
			
			echo "<pre>";
			print_r($agents);			
			echo "</pre>";

		}
		
		
		if ($api=="update_test6") {
			
			require_once '../library/Dialer/Dialer.php';
			$startofday=strtotime(date('Y-m-d')." 00:00:01");
			$endofday=strtotime(date('Y-m-d H:i:s'));
		
			$options['fields']='date,aid,hour,anm,ocis_sale,nc_all,nc_in,nc_out,nc_man,sec_talk,sec_wait,sec_wrap'; 
			$options['groupby']='hour,aid';
			//$options['groupby']='date,aid';
			$options['range']=$startofday.":".$endofday;
			$agents = api_reporting('calls', $options);
			
			
			//echo "<pre>";
			///print_r($agents);			
			//echo "</pre>";
			
			/*
				[date] => 2018-07-13
				[aid] => 0
				[hour] => 1
				[anm] => 
				[nc_all] => 1
				[nc_in] => 1
				[nc_out] => 0
				[nc_man] => 0
				[sec_talk] => 0
				[sec_wait] => 0
				[sec_wrap] => 0
                )
			*/
			$i=0;
			$kk=0;
			
			$hoursinday=array('05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
			foreach ($hoursinday as $lab) {
				if ($lab<=date('H',strtotime('+1 Hour'))) {
					$labels[$kk]=ltrim($lab,'0'); $kk++;
				}
			}
			foreach ($agents['list'] as $lista) {
				$houra=$lista['hour'];
				$source[$lista['aid']][$houra]=$lista;
				
				$source[$lista['aid']][$houra]['test']=$lista['nc_all'];
				if ($lista['aid']) {
					$total_received[$houra]=$total_received[$houra]+$lista['nc_all'];
				} else {
					$total_abandoned[$houra]=$total_abandoned[$houra]+$lista['nc_all'];
				}
					if ($total_received[$houra]==0) { $sm=1; } else { $sm=$total_received[$houra]; } 
					$percentage[$houra]=$total_abandoned[$houra]/$sm*100;
				
			}
			foreach ($agents['list'] as $list) {
				
				$aid=$list['aid'];
				$anm=$list['anm'];
				if ($anm) { $anmn=$anm; } else { $anmn="Abandoned Calls"; }
				
				$out[$aid]['label']=$anmn;
				$out[$aid]['yAxisID']="1";
				
				$randcolour=rand(0,255).','.rand(0,255).','.rand(0,255);
				
				$out[$aid]['backgroundColor']='rgb('.$randcolour.')';
				$out[$aid]['borderColor']='rgb('.$randcolour.')';
				$out[$aid]['fill']=false;
					$j=0;
					
					
					foreach ($labels as $key=>$hourin) {
						
						//if (!$source[$aid][$hourin]['nc_all']) {
						//	$out[$aid]['data'][$key]=0;
						//} else {
						//	$out[$aid]['data'][$key]=$source[$aid][$hourin]['nc_all'];
						//}
						
						if (!$source[$aid][$hourin]['test']) {
							$out[$aid]['data'][$key]=0;
						} else {
							$out[$aid]['data'][$key]=$source[$aid][$hourin]['test'];  
						}//
						$j++;
					}
				$i++;
			}
			
			$randcolour=rand(0,255).','.rand(0,255).','.rand(0,255);			
			
			foreach ($labels as $key=>$hourin) {
				if ($percentage[$hourin]) { $value=$percentage[$hourin]; } else { $value=0; } 
				$outp['data'][$key]=round($value,2);
				$out['perc']['backgroundColor']='rgb('.$randcolour.')';
				$out['perc']['borderColor']='rgb('.$randcolour.')';
				$out['perc']['fill']=false;
				$out['perc']['yAxisID']="2";
				$out['perc']['label']="Abandoned Call %";
				$out['perc']['data'][$key]=round($value,2);
			}
			
			$outp['labels']=$labels;
			$dataset['labels']=$labels;
			$dataset['datasets']=$out;
			$dataset['original']=$agents;
		
			
			?>
			<script src="http://www.chartjs.org/dist/2.7.2/Chart.bundle.js"></script>
			<script src="http://www.chartjs.org/samples/latest/utils.js"></script>
			<div style="width:90%; padding:30px; margin:20px;"><canvas id="canvas"></canvas></div>
			
			<script>
		var config = {
			type: 'bar',
			data: {
				labels: <?php echo json_encode($dataset['labels']); ?>,
				datasets: <?php echo json_encode(array_values($dataset['datasets'])); ?>
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: 'Reporting Calls Data'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					yAxes: [{
						type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
						display: true,
						position: 'left',
						id: '1',
						scaleLabel: {
							display: true,
							labelString: 'Calls Per Hour Per Agent'
						}
					}, {
						type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
						display: true,
						position: 'right',
						id: '2',
						gridLines: {
							drawOnChartArea: false
						},
						scaleLabel: {
							display: true,
							labelString: 'Average Abandoned Percentage'
						}
					}],
				}
			}
		};

		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myLine = new Chart(ctx, config);
		};

	



	</script>
	
			<?php
			
			echo "<pre>";
			print_r($outp);				
			echo "</pre>";
		}
				
		if ($api=="update_test7") {
			
			require_once '../library/Dialer/Dialer.php';
			$startofday=strtotime(date('Y-')."06-13 00:00:01");
			$endofday=strtotime(date('Y-m-d H:i:s'));
		
			$options['fields']='date,aid,hour,anm,ocis_sale,nc_all,nc_in,nc_out,nc_man,sec_talk,sec_wait,sec_wrap'; 
			$options['groupby']='date,aid';
			//$options['groupby']='date,aid';
			$options['range']=$startofday.":".$endofday;
			$agents = api_reporting('calls', $options);
			
			
			//echo "<pre>";
			///print_r($agents);			
			//echo "</pre>";
			
			/*
				[date] => 2018-07-13
				[aid] => 0
				[hour] => 1
				[anm] => 
				[nc_all] => 1
				[nc_in] => 1
				[nc_out] => 0
				[nc_man] => 0
				[sec_talk] => 0
				[sec_wait] => 0
				[sec_wrap] => 0
                )
			*/
			$i=0;
			$kk=0;
			
			$hoursinday=array('05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
			foreach ($hoursinday as $lab) {
				if ($lab<=date('H',strtotime('+1 Hour'))) {
					//$labels[$kk]=ltrim($lab,'0'); $kk++;
				}
			}
			
			  
			$date_from = $startofday; // Convert date to a UNIX timestamp  
			  
			// Specify the end date. This date can be any English textual format  
			$date_to = date('Y-m-d');  
			$date_to = strtotime('NOW');

			for ($ij=$date_from; $ij<=$date_to; $ij+=86400) {  
				$labels[$kk]=date("Y-m-d", $ij); $kk++;
				//echo date("Y-m-d", $ij).'<br />';  
			} 
			
			foreach ($agents['list'] as $lista) {
				$houra=$lista['date'];
				$source[$lista['aid']][$houra]=$lista;
			}
			foreach ($agents['list'] as $list) {
				
				$aid=$list['aid'];
				$anm=$list['anm'];
				if ($anm) { $anmn=$anm; } else { $anmn="Abandoned Calls"; }
				
				$out[$aid]['label']=$anmn;
				
				$randcolour=rand(0,255).','.rand(0,255).','.rand(0,255);
				
				$out[$aid]['backgroundColor']='rgb('.$randcolour.')';
				$out[$aid]['borderColor']='rgb('.$randcolour.')';
				$out[$aid]['fill']=false;
					$j=0;
					
					
					foreach ($labels as $key=>$hourin) {
						if (!$source[$aid][$hourin]['nc_all']) {
							$out[$aid]['data'][$key]=0;
						} else {
							$out[$aid]['data'][$key]=$source[$aid][$hourin]['nc_all'];
						}
						$j++;
					}
				$i++;
			}
			
			$dataset['labels']=$labels;
			$dataset['datasets']=$out;
			$dataset['original']=$agents;
		
			
			?>
			<script src="http://www.chartjs.org/dist/2.7.2/Chart.bundle.js"></script>
			<script src="http://www.chartjs.org/samples/latest/utils.js"></script>
			<canvas id="canvas"></canvas>
			
			<script>
		var config = {
			type: 'bar',
			data: {
				labels: <?php echo json_encode($dataset['labels']); ?>,
				datasets: <?php echo json_encode(array_values($dataset['datasets'])); ?>
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: 'Reporting Calls Data'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						stacked: true,
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Hour'
						}
					}],
					yAxes: [{
						stacked: true,
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Value'
						}
					}]
				}
			}
		};

		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myLine = new Chart(ctx, config);
		};

	



	</script>
	
			<?php
			echo "<pre>";
			print_r($dataset);			
			echo "</pre>";
		}