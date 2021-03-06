<!DOCTYPE html>
<html>
<head>
	<title>Laboratory Equipment Inventory Software System</title>

	<script src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery-ui-1.10.4.min.js"></script>
	<script src="<?php  echo base_url(); ?>js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.9.2.custom.min.js"></script>

	<script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>

	<script src="<?php echo base_url(); ?>js/jquery.scrollTo.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.nicescroll.js" type="text/javascript"></script>

	<script src="<?php echo base_url(); ?>js/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery-jvectormap-world-mill-en.js"></script> 
	<script src="<?php echo base_url(); ?>js/jquery.autosize.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.placeholder.min.js"></script> 
	<script src="<?php echo base_url(); ?>js/bootbox.min.js"></script> 

	<script type="text/javascript">

		var currentLab = 0;
		var labName ="";
		var labList=[];
		var labEquipments=[];
		var equipListLoad = "";
		var borrowedEquipListLoad = "";
		var borrowedEquipmentsArray = [];
		var damagedEquipmentsArray = [];
		var labNames = [];
		
		$(document).ready(function(){
			
			$("#all").click(function(){
				$("#all").addClass("active");
				$("#reports").removeClass("active");
				$(".lab").removeClass("active");
				$("#addBtn").show();		
				$("#addBtn").text("Add Laboratory");
				$("#frame").attr('src', "<?php echo site_url('Index/loadIframe/all');?>");
				currentLab=0;
			});			

			$("#addBtn").click(function(){
				$("#addBtn").attr({"data-toggle": "modal"});
				var htmlString = $(this).html();
				if(htmlString=="Add Laboratory"){
					$("#addBtn").attr("data-target", "#addLab");
				}
				if(htmlString=="Add Equipment"){
					$("#addBtn").attr("data-target", "#addEqpmnt");
				}
			});

			$("#reports").click(function(){
				$(this).addClass("active");
				$("#all").removeClass("active");
				$(".lab").removeClass("active");
				$("#addBtn").hide();	
				$("#frame").attr('src', "<?php echo site_url('Index/loadIframe/reports');?>");
			});		

			//Retrieve Laboratory List
			$.ajax({
				url: "<?php echo site_url('Laboratory/getLabList');?>",
				type: 'GET',
				dataType: 'json',
				success: function(data){
					if(data.length != 0){
						labList=data;
						var navigateLabs="";
						for(var i=0; i<labList.length; i++){
							labNames.push(labList[i].labName.toLowerCase());
							navigateLabs += "<tr><td><li class='lab' id='"+labList[i].labID+"' onclick='thisLab(this.id)''>";
							navigateLabs +="<a><i class='icon_grid-2x2'></i><span style='cursor: pointer;'>";
							navigateLabs += labList[i].labName + "</span></a></li></td></tr>";
						}
						$("#labList").html(navigateLabs);
					}else{}
				}
			});  

	        //Delete Laboratory Module
	        $("#deleteLab").click(function(){
	        	console.log("labtodelete "+ $("#currLab").val() );
	        	$.ajax({
	        		url: "<?php echo site_url('Equipment/checkLabData');?>",
	        		type: 'POST',
	        		data: {'search': 'allEquipments',
	        				'labID': $("#currLab").val() },
	        		dataType: 'json',
	        		success: function(data){
	        			console.log(data)
	        			if(data == 'canBeDeleted'){
	        				$.ajax({
	        					url: "<?php echo site_url('Laboratory/deleteLab');?>",
	        					type: 'POST',
	        					data: {'labID': $("#currLab").val()},						
	        					success: function(data){
	        						console.log("Laboratory "+$("#currLab").val()+" Successfully Deleted");					
	        					}
	        				});
	        				alert("Successfully Deleted Laboratory");
	      //   				bootbox.alert({
							//     message: "<p align='center'>Successfully Deleted Laboratory!</p>",
							//     backdrop: true
							// });	
	        				window.top.location.href = "http://localhost/liss/"; 
	        			}else{
	        				// alert("The laboratory cannot be deleted. For a laboratory to be deleted, make sure it contains no equipments.");
	        				bootbox.alert({
							    message: "<p align='center'>The laboratory cannot be deleted. For a laboratory to be deleted, make sure it contains no equipments.</p>",
							    backdrop: true
							});	
	        			}
	        		}
	        	});
	        });


			// Add Laboratory Module
			var validLab=false;
			$("#labName").keyup(function(){
				var check = /^[0-9a-zA-Z]+$/;
				if($(this).val().length > 0 && $(this).val().match(check)){
					if(labNames.indexOf($(this).val().toLowerCase()) >= 0){
						validLab=true;
						console.log("Laboratory name already exists")
						$("#labNameValidate").html("Laboratory name already exists");
						validLab=false;
					}else{
						$("#labNameValidate").html("");
					}
	        	}else if(!$(this).val().match(check) && $(this).val().length > 0){
	        		$("#labNameValidate").html("Laboratory name contains invalid characters.");
	        	}else if($(this).val().length == 0){
	        		$("#labNameValidate").html("Laboratory name cannot be empty.");
	        	}
	        });

			$(function(){
				$("#addLabBtn").click(function(){
					if(!$("#labName").val()){
						// alert("You may have left the name empty or the laboratory already exists.");
						bootbox.alert({
							message: "<p align='center'>You may have left the name empty or the laboratory already exists.</p>",
							backdrop: true
						});	
					}
					else if($("#labNameValidate").html() == ""){
		       			$.ajax({
		       				url: "<?php echo site_url('Laboratory/addLab');?>",
		       				type: 'POST',
		       				data: {'labName': $("#labName").val(),
		       				'description': $("#description").val()},						
		       				success: function(data){}
		       			});
		       			alert("Laboratory successfully added.");
		    //    			bootbox.alert({
						// 	message: "<p align='center'>Laboratory successfully added.</p>",
						// 	backdrop: true
						// });	
		       			location.reload();
		       		}
		       		$("#labName").empty();
		       		$("#description").empty();
		       	});
			});		

			var allEquips = [];
	        $.ajax({
	        	url: "<?php echo site_url('Equipment/getAllEquipments');?>",
	        	type: 'GET',
	        	dataType: 'json',
	        	success: function(data){
	        		for(var i = 0; i < data.length; i++){
	        			allEquips.push(data[i].split(" - ")[0]);
	        		}
	        	}
	        });

	        $("#eqpSerialNum").keyup(function(){
	        	if($(this).val().length > 0){
	        		 $.ajax({
			        	url: "<?php echo site_url('Equipment/getAllEquipments');?>",
			        	type: 'GET',
			        	dataType: 'json',
			        	success: function(data){
			        		for(var i = 0; i < data.length; i++){
			        			allEquips.push(data[i].split(" - ")[0]);
			        		}
			        	}
			        });
	        		if(allEquips.indexOf($(this).val().toLowerCase()) >= 0){
	        			$("#serialNumValidate").html("Item already exists");
	        		}else{
	        			console.log('does not')
	        			$("#serialNumValidate").html("");
	        		}
	        	}else{
	        		$("#serialNumValidate").html("");
	        	}
	        });

	       	$("#addEquipmentBtn").click(function(e){
	       		if($("#eqpSerialNum").val() != '' && $("#eqpName").val() != '' && $("#eqpPrice").val() != '' && $("#serialNumValidate").html() != "Item already exists"){
	       			$(this).unbind('submit').submit();
	       			e.preventDefault();
	       			if($("input[name=item]:checked")[0].id == "equipment"){
	       				$.ajax({
	       					url: "<?php echo site_url('Equipment/addEquipment');?>",
	       					type: 'POST',
	       					data: {'eqpSerialNum': $("#eqpSerialNum").val(),
			       					'eqpName': $("#eqpName").val(),
			       					'labID': $("#currLab").val(),
			       					'type': $("input[name=item]:checked").val(),
			       					'eqpPrice':  $("#eqpPrice").val()},
	       					success: function(data){
	       						console.log(data);
	       						var e = [];
	       						e.push($("#eqpSerialNum").val());
	       						$.ajax({
	       							url: "<?php echo site_url('Reports/storeLog');?>",
	       							type: 'POST',
	       							data: {
	       								'studentID': '0',
	       								'equipment': e,
	       								'action': 'add',
	       								'labID': $("#currLab").val()
	       							},
	       							success: function(data){}
	       						});
	       					}	
	       				});
	       			}else {
	       				$.ajax({
	       					url: "<?php echo site_url('Equipment/addEquipmentComp');?>",
	       					type: 'POST',
	       					data: {'compSerialNum': $("#eqpSerialNum").val(),
			       					'compName': $("#eqpName").val(),
			       					'labID': $("#currLab").val(),
			       					'type': $("input[name=item]:checked").val(),
			       					'compPrice':  $("#eqpPrice").val()},
	       					success: function(data){
	       						var e = [];
	       						e.push($("#eqpSerialNum").val());
	       						$.ajax({
	       							url: "<?php echo site_url('Reports/storeLog');?>",
	       							type: 'POST',
	       							data: {
	       								'studentID': '0',
	       								'equipment': e,
	       								'action': 'add',
	       								'labID': $("#currLab").val()
	       							},
	       							success: function(data){}
	       						});
	       					}
	       				});	
	       			}
	       			$("#addEqpmnt").removeClass("in");
	       			$(".modal-backdrop").remove();
	       			$(".modal-backdrop").hide();
	       			$("#addEqpmnt").find("input[id='equipment']").prop('checked', 'checked');
	       			alert("Equipment Successfully Added!");
	    //    			bootbox.alert({
					// 		    message: "<p align='center'>Equipment Successfully Added!</p>",
					// 		    backdrop: true
					// });
	       			location.reload();
	       		}else if($("#serialNumValidate").html() == "Item already exists"){
	       			e.preventDefault();
	       		}else{
					// e.preventDefault();
				}
			});
			//END Add Equipment Module


			// Edit Equipment Module
			$("#editSaveBtn").click(function(e){
				if($("#editName").val() != '' && $("#editPrice").val() != ''){
					$(this).unbind('submit').submit();
					e.preventDefault();
					$.ajax({
						url: "<?php echo site_url('Equipment/updateEquipment');?>",
						type: 'POST',
						data: {'eqpSerialNum': $("#editSerialNum").val(),
						'eqpName': $("#editName").val(),
						'eqpPrice': $("#editPrice").val()
					},
					success: function(data){
						var e = [];
						e.push($("#editSerialNum").val());
						$.ajax({
							url: "<?php echo site_url('Reports/storeLog');?>",
							type: 'POST',
							data: {
								'studentID':'0',
								'equipment': e,
								'action': 'edit',
								'labID': $("#currLab").val()
							},
							success: function(data){}
						});
						console.log(data);
						$("#editModal").modal('hide');
						alert("Equipment Successfully Updated!");
						// bootbox.alert({
						// 	    message: "<p align='center'>Equipment Successfully Updated!</p>",
						// 	    backdrop: true
						// });	
			        		// $("#frame").attr('src', "<?php echo site_url('Index/loadIframe/lab');?>");
			        		location.reload();
			        	}
			        });	
				}
				
			});
	        // END Edit Equipment Module

	        var eqplist=[];
	        //View All
	        $.ajax({
	        	url: "<?php echo site_url('Equipment/getAllEquipments');?>",
	        	type: 'GET',
	        	dataType: 'json',
	        	success: function(data){
	        		// console.log(data);
	        		// allEquips = data;
	        		$("#searchAll").autocomplete({
	        			source: data,
		                 //if empty results
		                 response: function(event, ui) {
		                 	console.log(event.keyCode);
		                 	if (ui.content.length === 0) {
		                 		var noResult = { value:"",label:"No results found" };
		                 		ui.content.push(noResult);
		                 	} else {
		                         // $("#empty-message").empty();
		                     }
		                 },
		                 select: function(event, ui) {
		                 	var thisEquipment = ui.item.value.split(" - ");
		                 	$.ajax({
		                 		url: "<?php echo site_url('Equipment/searchEquipmentAll');?>",
		                 		type: 'POST',
		                 		data: {'equipmentSerialNum': thisEquipment[0],
		                 		'equipmentName': thisEquipment[1]
		                 	},
		                 	success: function(data){
		                 		console.log(data);
		                 		viewAllEquipments(data);
		                 		eqplist=data;
		                 	}
		                 });
		                 }
		             });
	        	}
	        });
	        $("#searchAll").keyup(function(e){
	        	if(e.which == 13) {
	        		console.log("enter");
	      		// viewLaboratoryEquipments(eqplist);
	      	}
	      	if('' == $("#searchAll").val()){
	      		$.ajax({
	      			url: "<?php echo site_url('Equipment/getAllEquipmentsMain');?>",
	      			type: 'GET',
	      			dataType: 'json',
	      			success: function(data){
	      				console.log(data);
	      				$("#headEquipments").html('<tr><th class="th"><i class="icon_clipboard"></i> Name</th><th class="th"><i class="icon_datareport_alt"></i> Quantity</th></tr>');
	      				var searchResult = "";
	      				for(var i = 0; i < data[0].length; i++){
	      					searchResult += "<tr>";
	      					searchResult += "<td>"+data[0][i].eqpName+"</td>";
	      					searchResult += "<td>"+data[0][i].quantity+"</td>";
	      					searchResult += "</tr>";
	      				}
	      				for(var i = 0; i < data[1].length; i++){
	      					searchResult += "<tr>";
	      					searchResult += "<td>"+data[1][i].compName+"</td>";
	      					searchResult += "<td>"+data[1][i].quantity+"</td>";
	      					searchResult += "</tr>";
	      				}
	      				$("#allEquipments").html(searchResult);
	      			}
	      		});  
	      	}else{}
	      }); 

	        function viewAllEquipments(data){
	        	console.log(data);
	        	$("#headEquipments").html('<tr><th class="th"><i class="icon_clipboard"></i> Name</th><th class="th"><i class="icon_datareport_alt"></i> Quantity</th></tr>');
	        	var searchResult = "";
	        	for(var i = 0; i < data.length; i++){
	        		searchResult += "<tr>";
	        		searchResult += "<td>"+data[i].eqpName+"</td>";
	        		searchResult += "<td>"+data[i].quantity+"</td>";
	        		searchResult += "</tr>";
	        	}
	        	$("#allEquipments").html(searchResult);
	        }  
			// Viewing Laboratory 
			$.ajax({
				url: "<?php echo site_url('Equipment/getEquipments');?>",
				type: 'POST',
				data: {'search': 'allEquipments',
				'labID': $("#currLab").val() },
				dataType: 'json',
				success: function(data){
					$("#searchEquipment").autocomplete({
						source: data,
	                 //if empty results
	                 response: function(event, ui) {
	                 	if (ui.content.length === 0) {
	                 		var noResult = { value:"",label:"No results found" };
	                 		ui.content.push(noResult);
	                 	} else {
	                         // $("#empty-message").empty();
	                     }
	                 },
	                 select: function(event, ui) {
	                 	var thisEquipment = ui.item.value.split(" - ");
	                 	$.ajax({
	                 		url: "<?php echo site_url('Equipment/searchEquipment');?>",
	                 		type: 'POST',
	                 		data: {'equipmentSerialNum': thisEquipment[0],
	                 		'equipmentName': thisEquipment[1]
	                 	},
	                 	success: function(data){
	                 		console.log(data);
	                 		viewLaboratoryEquipments(data);
	                 		eqplist=data;
	                 	}
	                 });
	                 }
	             });
				}
			});

	      // Search Equipment module
	      $("#searchEquipment").keyup(function(e){
	      	if(e.which == 13) {
	      		console.log("enter");
	      		// viewLaboratoryEquipments(eqplist);
	      	}
	      	if('' == $("#searchEquipment").val()){
	      		$.ajax({
	      			url: "<?php echo site_url('Equipment/getEquipments');?>",
	      			type: 'POST',
	      			data: {'search': 'allEquipments',
	      			'labID': $("#currLab").val() },
	      			dataType: 'json',
	      			success: function(data){
	      				var eq=[];
	      				console.log(data);
	      				for(var i = 0; i < data.length; i++){
	      					var eqp = data[i].split(" - ");
	      					var item = {
	      						eqpSerialNum : eqp[0],
	      						eqpName  :eqp[1]
	      					};
	      					eq.push(item);
	      				}
	      				viewLaboratoryEquipments2(eq);
	      			}
	      		});  
	      	}else{}
	      }); 
	      var check = [];
	      var checkAll = [];
	      var type = [];
	      var table = document.getElementById("labEquipmentsTable").getElementsByClassName('itemDetails'); 

	      for(var i = 0; i < table.length; i++){
	      	if($("#labEquipmentsTable tbody .itemDetails")[i].getElementsByTagName('input').length == 1){
	      		check.push(table[i].children[0].textContent);
	      	}
	      	checkAll.push(table[i].children[0].textContent);
	      	type.push(table[i].children[2].textContent);						
	      }

	      function viewLaboratoryEquipments(data){
	      	console.log(type);
	      	var searchResult = "";
	      	for(var i = 0; i < data.length; i++){
	      		searchResult += "<tr class='itemDetails' id='"+data[i].eqpSerialNum+"tr'>";
	      		searchResult += "<td>"+data[i].eqpSerialNum+"</td>";
	      		searchResult += "<td>"+data[i].eqpName+"</td>";
	      		searchResult += "<td>";
	      		var ndx = checkAll.indexOf(data[i].eqpSerialNum);
	      		searchResult += type[ndx];
	      		searchResult +="</td>";
	      		searchResult += '<td><div class="btn-group"><a class="btn btn-primary" onclick = "editEquipment(\''+data[i].eqpSerialNum+'\')" rel="tooltip" title="Edit"><i class="icon_pencil"></i></a><a class="btn btn-success" data-target="#vehModal" data-toggle="modal" rel="tooltip" onclick = "viewEquipmentHistory(\''+data[i].eqpSerialNum+'\', \''+data[i].eqpName+'\')" id="'+data[i].eqpSerialNum+'"  value="'+data[i].eqpSerialNum+'" title="View Equipment History"><i class=" icon_search-2" ></i></a></div>';
	      		if(data[i].borrowed == 0 && data[i].damaged == 0){
	      			searchResult +='<input type="checkbox" class="check equipCheck" name="checkItem" id="'+data[i].eqpSerialNum+'checkbox" onclick="moveAll(this.id)">';
	      		}
	      		searchResult += '</td>';
	      		searchResult += "</tr>";
	      	}
	      	$("#labEquipmentsTable tbody").html(searchResult);
	      	var pager = new Pager('labEquipmentsTable', 5);
	      	pager.init(); 
	      	pager.showPageNav('pager', 'pageNavPosition'); 
	      	pager.showPage(1);
	      }

	      function viewLaboratoryEquipments2(data){
	      	console.log('check', check);
	      	var searchResult = "";
	      	for(var i = 0; i < data.length; i++){
	      		searchResult += "<tr class='itemDetails' id='"+data[i].eqpSerialNum+"tr'>";
	      		searchResult += "<td>"+data[i].eqpSerialNum+"</td>";
	      		searchResult += "<td>"+data[i].eqpName+"</td>";
	      		searchResult += "<td>";
	      		var ndx = checkAll.indexOf(data[i].eqpSerialNum);
	      		searchResult += type[ndx];
	      		searchResult +="</td>";
	      		searchResult += '<td><div class="btn-group"><a class="btn btn-primary" onclick = "editEquipment(\''+data[i].eqpSerialNum+'\')" rel="tooltip" title="Edit"><i class="icon_pencil"></i></a><a class="btn btn-success" data-target="#vehModal" data-toggle="modal" rel="tooltip" onclick = "viewEquipmentHistory(\''+data[i].eqpSerialNum+'\', \''+data[i].eqpName+'\')" id="'+data[i].eqpSerialNum+'"  value="'+data[i].eqpSerialNum+'" title="View Equipment History"><i class=" icon_search-2" ></i></a></div>';
	      		if(check.indexOf(data[i].eqpSerialNum) >= 0){
	      			searchResult +='<input type="checkbox" class="check equipCheck" name="checkItem" id="'+data[i].eqpSerialNum+'checkbox" onclick="moveAll(this.id)">';
	      		}
	      		searchResult += '</td>';
	      		searchResult += "</tr>";
	      	}
	      	$("#labEquipmentsTable tbody").html(searchResult);
	      	check = [];
	      	var pager = new Pager('labEquipmentsTable', 5);
	      	pager.init(); 
	      	pager.showPageNav('pager', 'pageNavPosition'); 
	      	pager.showPage(1);
	      }
	        //  END Search Equipment module

	        //File Damaged Equipment Module
	        $("#fde").click(function(){
	        	$.ajax({
	        		url: "<?php echo site_url('Equipment/getEquipments');?>",
	        		type: 'POST',
	        		data: {'search': 'available',
	        		'labID': $("#currLab").val()},
	        		dataType: 'json',
	        		success: function(data){
	        			$("#searchDamaged").autocomplete({          
	        				source: data,
	                 //if empty results
	                 response: function(event, ui) {
	                 	if (ui.content.length === 0) {
	                 		var noResult = { value:"",label:"No results found" };
	                 		ui.content.push(noResult);
	                 	} else {
	                         // $("#empty-message").empty();
	                     }
	                 },
	                 select: function(event, ui) {
	                 	var thisEquipment = ui.item.value.split(" - ");
	                 	$.ajax({
	                 		url: "<?php echo site_url('Equipment/searchEquipment');?>",
	                 		type: 'POST',
	                 		data: {'equipmentSerialNum': thisEquipment[0],
	                 		'equipmentName': thisEquipment[1]
	                 	},
	                 	success: function(data){
	                 		console.log(data);
	                 		var equipList = "";
	                 		if(data.length > 0){
	                 			for(var i = 0; i < data.length; i++){
	                 				equipList += "<tr>";
	                 				equipList += "<td>"+data[i].eqpSerialNum+" "+data[i].eqpName+"</td>";
	                 				equipList += "<td><input type='checkbox' class='boxCheckDamage' onclick='checkDamage(this, "+data[i].eqpSerialNum+")' id='"+data[i].eqpSerialNum+"' value='"+data[i].eqpSerialNum+'-'+data[i].eqpName+'-'+data[i].price+"'></td>";
	                 				equipList += "</tr>";  
	                 			}
	                 			$(".damageItem").css('display', 'none');
	                 			$("#damagedList").html(equipList);

	                 			if(damagedEquipmentsArray.length != 0){
	                 				for(var j = 0; j < damagedEquipmentsArray.length; j++){
	                 					for(var k = 0; k < $("#damagedList .boxCheckDamage").length; k++){
	                 						if($("#damagedList .boxCheckDamage")[k].id == damagedEquipmentsArray[j]){
	                 							$("#damagedList #"+damagedEquipmentsArray[j]).prop('checked', true);
	                 						}
	                 					}
	                 				}
	                 			}

	                 		}
	                 	}
	                 });
	                 }
	             });
	        		}
	        	});
	        });

	        $("#searchDamaged").keyup(function(){
	        	if('' == $(this).val()){
	        		$.ajax({
	        			url:"<?php echo site_url('Equipment/getAvailableEquipments');?>",
	        			type: 'POST',
	        			data: {'search': 'available',
	        			'labID': $("#currLab").val()},
	        			dataType: 'json',
	        			success: function(data){
	        				var equipList = "";
	        				if(data[0].length > 0 || data[1].length > 0){
	        					if(data[0].length > 0){
	        						for(var i = 0; i < data[0].length; i++){
	        							equipList += "<tr>";
	        							equipList += "<td>"+data[0][i].eqpSerialNum+" "+data[0][i].eqpName+"</td>";
	        							equipList += "<td><input type='checkbox' class='boxCheckDamage' onclick='checkDamage(this, "+data[0][i].eqpSerialNum+")' id='"+data[0][i].eqpSerialNum+"' value='"+data[0][i].eqpSerialNum+'-'+data[0][i].eqpName+'-'+data[0][i].price+"'></td>";
	        							equipList += "</tr>";  
	        						}
	        					}
	        					if(data[1].length > 0){
	        						for(var i = 0; i < data[1].length; i++){
	        							equipList += "<tr>";
	        							equipList += "<td>"+data[1][i].eqpSerialNum+" "+data[1][i].eqpName+"</td>";
	        							equipList += "<td><input type='checkbox' class='boxCheckDamage' onclick='checkDamage(this, "+data[1][i].eqpSerialNum+")' id='"+data[1][i].eqpSerialNum+"' value='"+data[1][i].eqpSerialNum+'-'+data[1][i].eqpName+'-'+data[1][i].price+"'></td>";
	        							equipList += "</tr>";  
	        						}
	        					}
	        					$(".damageItem").css('display', 'block');
	        					$("#damagedEquipList .damageItem").attr('checked', false);
	        					$("#damagedList").html(equipList);
	        					
	        					if(damagedEquipmentsArray.length != 0){
	        						for(var j = 0; j < damagedEquipmentsArray.length; j++){
	        							for(var k = 0; k < $("#damagedList .boxCheckDamage").length; k++){
	        								if($("#damagedList .boxCheckDamage")[k].id == damagedEquipmentsArray[j]){
	        									$("#damagedList #"+damagedEquipmentsArray[j]).prop('checked', true);
	        								}
	        							}
	        						}
	        					}

	        					// $("#damagedEquipments").html('');
	        					// $("#price").html(0);
	        				}
	        			}
	        		});  
	        	}
	        	else{

	        	}
	        }); 

	        $("#fde").click(function(){
	        	$.ajax({
	        		url: "<?php echo site_url('Equipment/getAvailableEquipments');?>",
	        		type: 'POST',
	        		data: {'search': 'available',
	        		'labID': $("#currLab").val()},
	        		dataType: 'json',
	        		success: function(data){
	        			console.log('fde2', data);
	        			var equipList = "";
	        			if(data[0].length > 0 || data[1].length > 0){
	        				if(data[0].length > 0){
	        					for(var i = 0; i < data[0].length; i++){
	        						equipList += "<tr>";
	        						equipList += "<td>"+data[0][i].eqpSerialNum+" "+data[0][i].eqpName+"</td>";
	        						equipList += "<td><input type='checkbox' class='boxCheckDamage' onclick='checkDamage(this, "+data[0][i].eqpSerialNum+")' id='"+data[0][i].eqpSerialNum+"' value='"+data[0][i].eqpSerialNum+'-'+data[0][i].eqpName+'-'+data[0][i].price+"'></td>";
	        						equipList += "</tr>";  
	        					}
	        				}
	        				if(data[1].length > 0){
	        					for(var i = 0; i < data[1].length; i++){
	        						equipList += "<tr>";
	        						equipList += "<td>"+data[1][i].eqpSerialNum+" "+data[1][i].eqpName+"</td>";
	        						equipList += "<td><input type='checkbox' class='boxCheckDamage' onclick='checkDamage(this, "+data[1][i].eqpSerialNum+")' id='"+data[1][i].eqpSerialNum+"' value='"+data[1][i].eqpSerialNum+'-'+data[1][i].eqpName+'-'+data[1][i].price+"'></td>";
	        						equipList += "</tr>";  
	        					}
	        				}
	        				$("#damagedList").html(equipList);
	        				$("#price").html(0);
	        				equipListLoad = equipList;
	        			}else{
	        				$("#damagemModalHeader").css('display', 'none');
	        			}
	        		}
	        	});  
	        });    

	        $("#damageBtn").click(function(e){
	        	if($("#damagerID").val() != '' && $("#damagerName").val() != '' && $("#damagerTeacher").val() != ''  && ($(".nameValidate").text() == '' && $(".teacherValidate").text() == '')){
	        		var items = document.getElementById("damagedEquipments").getElementsByClassName('damagedListClass'); 
	        		var equipments = [];
	        		$(this).unbind('submit').submit();
	        		var damagedEqps = '';
	        		for(var i = 0; i < items.length; i++){
	        			console.log(items[i].id.replace('id', ''));
	        			equipments.push(items[i].id.replace('id', ''));
	        			damagedEqps += '<span style="font-weight: bold;" >'+$("#damagedEquipments tr td:first-child")[i].textContent+'</span><br>';
	        		}
	        		if(equipments.length != 0){  
	        			e.preventDefault();
	        			$.ajax({
	        				url: "<?php echo site_url('Student/checkIDNum');?>",
	        				type: 'POST',
	        				data: {'studentID': $("#damagerID").val()},
	        				success: function(data){
	        					if(0 == data.length){
	        						if (confirm('A new student data will be added. Are you sure you want to add this?')) {
	        							console.log('insert new student record');
	        							$.ajax({
	        								url: "<?php echo site_url('Student/addDamage');?>",
	        								type: 'POST',
	        								data: {'damagerID': $("#damagerID").val(),
	        								'damagerName': $("#damagerName").val()
	        							},
	        							success: function(data){}
	        						});
	        						}
	        					}
	        					$.ajax({
	        						url: "<?php echo site_url('DamageList/addDamageEquipments');?>",
	        						type: 'POST',
	        						data: {
	        							'damagerID': $("#damagerID").val(),
	        							'equipment': equipments,
	        							'damagerTeacher': $("#damagerTeacher").val(),
	        							'labID': $("#currLab").val()
	        						},
	        						success: function(data){
	        							$.ajax({
	        								url: "<?php echo site_url('Reports/storeLog');?>",
	        								type: 'POST',
	        								data: {
	        									'studentID': $("#damagerID").val(),
	        									'equipment': equipments,
	        									'action': 'damage',
	        									'labID': $("#currLab").val()
	        								},
	        								success: function(data){
	        								}
	        							});

	        							$("#damageModal").modal('hide');
	        							$("#notifyModal").modal('show');
	        							$("#notifyModal .notifyHeader").html('Successfully Filed as Damage');
	        							$("#notifyModal #divContent").html(damagedEqps);  
	        							$("#notifyModal").on('hidden.bs.modal', function (e) {
	        								location.reload();
	        							});
	        						}
	        					}); 
	        				}
	        			});
	        		}else{
	        			//alert('Choose equipment(s)');
	        			bootbox.alert({
							    message: "<p align='center'>Choose equipment(s)</p>",
							    backdrop: true
						});
	        			e.preventDefault();
	        		}   					
	        	}else if($(".nameValidate").text() != '' || $(".teacherValidate").text() != ''){
	        		//alert('Something went wrong. Check inputs.');
	        		bootbox.alert({
							    message: "<p align='center'>Something went wrong. Check inputs.</p>",
							    backdrop: true
					});
	        		e.preventDefault();
	        	}
	        });
			// END File Damage Equipment Module         

			//Borrow Equipment Module
			$("#borrow").click(function(){
				$.ajax({
					url: "<?php echo site_url('Equipment/getEquipments');?>",
					type: 'POST',
					data: {'search': 'available',
					'labID': $("#currLab").val()},
					dataType: 'json',
					success: function(data){
						$("#searchBorrowed").autocomplete({          
							source: data,
	                 //if empty results
	                 response: function(event, ui) {
	                 	if (ui.content.length === 0) {
	                 		var noResult = { value:"",label:"No results found" };
	                 		ui.content.push(noResult);
	                 	} else {
	                         // $("#empty-message").empty();
	                     }
	                 },
	                 select: function(event, ui) {
	                 	var thisEquipment = ui.item.value.split(" - ");
	                 	$.ajax({
	                 		url: "<?php echo site_url('Equipment/searchEquipment');?>",
	                 		type: 'POST',
	                 		data: {'equipmentSerialNum': thisEquipment[0],
	                 		'equipmentName': thisEquipment[1]
	                 	},
	                 	success: function(data){
	                 		console.log(data);
	                 		var equipList = "";
	                 		if(data.length > 0){
	                 			for(var i = 0; i < data.length; i++){
	                 				equipList += "<tr>";
	                 				equipList += "<td>"+data[i].eqpSerialNum+" "+data[i].eqpName+"</td>";
	                 				equipList += "<td><input type='checkbox' class='boxCheck' onclick='checkBorrow(this, "+data[i].eqpSerialNum+")' id='"+data[i].eqpSerialNum+"' value='"+data[i].eqpSerialNum+'-'+data[i].eqpName+'-'+data[i].price+"'></td>";
	                 				equipList += "</tr>";  
	                 			}
	                 			$(".returnItem").css('display', 'none');
	                 			$("#borrowedList").html(equipList);

	                 			if(borrowedEquipmentsArray.length != 0){
	                 				for(var j = 0; j < borrowedEquipmentsArray.length; j++){
	                 					for(var k = 0; k < $("#borrowedList .boxCheck").length; k++){
	                 						if($("#borrowedList .boxCheck")[k].id == borrowedEquipmentsArray[j]){
	                 							$("#borrowedList #"+borrowedEquipmentsArray[j]).prop('checked', true);
	                 							console.log($(".boxCheck")[k]);
	                 						}
	                 					}
	                 				}
	                 			}
	                 		}
	                 	}
	                 });
	                 }
	             });
					}
				});
			});

			$("#searchBorrowed").keyup(function(){
				console.log($(this).val());
				if('' == $(this).val()){
					$.ajax({
						url:"<?php echo site_url('Equipment/getAvailableEquipments');?>",
						type: 'POST',
						data: {'search': 'available',
						'labID': $("#currLab").val()},
						dataType: 'json',
						success: function(data){
							console.log(borrowedEquipmentsArray);
							var equipList = "";
							if(data[0].length > 0 || data[1].length > 0){
								if(data[0].length > 0){
									for(var i = 0; i < data[0].length; i++){
										equipList += "<tr>";
										equipList += "<td>"+data[0][i].eqpSerialNum+" "+data[0][i].eqpName+"</td>";
										equipList += "<td><input type='checkbox' class='boxCheck' onclick='checkBorrow(this, "+data[0][i].eqpSerialNum+")' id='"+data[0][i].eqpSerialNum+"' value='"+data[0][i].eqpSerialNum+'-'+data[0][i].eqpName+'-'+data[0][i].price+"'></td>";
										equipList += "</tr>";  
									}
								}
								if(data[1].length > 0){
									for(var i = 0; i < data[1].length; i++){
										equipList += "<tr>";
										equipList += "<td>"+data[1][i].eqpSerialNum+" "+data[1][i].eqpName+"</td>";
										equipList += "<td><input type='checkbox' class='boxCheck' onclick='checkBorrow(this, "+data[1][i].eqpSerialNum+")' id='"+data[1][i].eqpSerialNum+"' value='"+data[1][i].eqpSerialNum+'-'+data[1][i].eqpName+'-'+data[1][i].price+"'></td>";
										equipList += "</tr>";  
									}
								}
								$(".returnItem").css('display', 'block');
								$("#borrowedEquipList .returnItem").attr('checked', false);
								$("#borrowedList").html(equipList);

								if(borrowedEquipmentsArray.length != 0){
									for(var j = 0; j < borrowedEquipmentsArray.length; j++){
										for(var k = 0; k < $("#borrowedList .boxCheck").length; k++){
											if($("#borrowedList .boxCheck")[k].id == borrowedEquipmentsArray[j]){
												$("#borrowedList #"+borrowedEquipmentsArray[j]).prop('checked', true);
												console.log($(".boxCheck")[k]);
											}
										}
									}
								}

	        					// $("#borrowedEquipments").html('');
	        					// $("#borrowedPrice").html(0);
	        				}
	        			}
	        		});  
				}else{}
			}); 

			$("#borrow").click(function(){
				$.ajax({
					url: "<?php echo site_url('Equipment/getAvailableEquipments');?>",
					type: 'POST',
					data: {'search': 'available',
					'labID': $("#currLab").val()},
					dataType: 'json',
					success: function(data){
						console.log(data);
						var equipList = "";
						if(data[0].length > 0 || data[1].length > 0){
							if(data[0].length > 0){
								for(var i = 0; i < data[0].length; i++){
									equipList += "<tr>";
									equipList += "<td>"+data[0][i].eqpSerialNum+" "+data[0][i].eqpName+"</td>";
									equipList += "<td><input type='checkbox' class='boxCheck' onclick='checkBorrow(this, "+data[0][i].eqpSerialNum+")' id='"+data[0][i].eqpSerialNum+"' value='"+data[0][i].eqpSerialNum+'-'+data[0][i].eqpName+'-'+data[0][i].price+"'></td>";
									equipList += "</tr>";  
								}
							}
							if(data[1].length > 0){
								for(var i = 0; i < data[1].length; i++){
									equipList += "<tr>";
									equipList += "<td>"+data[1][i].eqpSerialNum+" "+data[1][i].eqpName+"</td>";
									equipList += "<td><input type='checkbox' class='boxCheck' onclick='checkBorrow(this, "+data[1][i].eqpSerialNum+")' id='"+data[1][i].eqpSerialNum+"' value='"+data[1][i].eqpSerialNum+'-'+data[1][i].eqpName+'-'+data[1][i].price+"'></td>";
									equipList += "</tr>";  
								}
							}
							$("#borrowedList").html(equipList);
							borrowedEquipListLoad = equipList;
						}else{
							$("#borrowmModalHeader").css('display', 'none');
						}
					}
				});
			});   

			$("#borrowBtn").click(function(e){
				if($("#borrowerID").val() != '' && $("#borrowerName").val() != '' && $("#borrowerTeacher").val() != '' && $("#incharge").val() != '' && ($(".nameValidate").text() == '' && $(".teacherValidate").text() == '' && $(".inchargeValidate").text() == '')){
					$(this).unbind('submit').submit();
					var items = document.getElementById("borrowedEquipments").getElementsByClassName('borrowedListClass'); 
					var equipments = [];
					var borrowedEqps = '';
					for(var i = 0; i < items.length; i++){
						console.log(items[i].id.replace('id', ''));
						equipments.push(items[i].id.replace('id', ''));
						borrowedEqps += '<span style="font-weight: bold;" >'+$("#borrowedEquipments td")[i].textContent+'</span><br>';
					}
					if(equipments.length != 0){  
						e.preventDefault();
						$.ajax({
							url: "<?php echo site_url('Student/checkIDNum');?>",
							type: 'POST',
							data: {'studentID': $("#borrowerID").val()},
							success: function(data){
								if(0 == data.length){
									if (confirm('A new student data will be added. Are you sure you want to add this?')) {
										console.log('insert new student record');
										$.ajax({
											url: "<?php echo site_url('Student/addBorrower');?>",
											type: 'POST',
											data: {'bidnum': $("#borrowerID").val(),
											'bname': $("#borrowerName").val()
										},
										success: function(data){}
									});
									}
								}
								$.ajax({
									url: "<?php echo site_url('BorrowList/addBorrowedEquipments');?>",
									type: 'POST',
									data: {
										'borrowerID': $("#borrowerID").val(),
										'equipment': equipments,
										'bteacher': $("#borrowerTeacher").val(),
										'incharge': $("#incharge").val(),
										'labID': $("#currLab").val()
									},
									success: function(data){
										$.ajax({
											url: "<?php echo site_url('Reports/storeLog');?>",
											type: 'POST',
											data: {
												'studentID': $("#borrowerID").val(),
												'equipment': equipments,
												'action': 'borrow',
												'labID': $("#currLab").val()
											},
											success: function(data){
											}
										});

										$("#borrowModal").modal('hide');
										$("#notifyModal").modal('show');
										$("#notifyModal .notifyHeader").html('Equipment(s) Successfully Borrowed');
										$("#notifyModal #divContent").html(borrowedEqps);  
										$("#notifyModal").on('hidden.bs.modal', function (e) {
											location.reload();
										});
									}
								});  
							}
						});	
					}else{
						//alert('Choose equipment(s)');
						bootbox.alert({
							    message: "<p align='center'>Choose equipment(s)</p>",
							    backdrop: true
						});
						e.preventDefault();
					}   
				}else if($(".nameValidate").text() != '' || $(".teacherValidate").text() != '' || $(".inchargeValidate").text() != ''){
					//alert('Something went wrong. Check inputs.');
					bootbox.alert({
							    message: "<p align='center'>Something went wrong. Check inputs.</p>",
							    backdrop: true
					});
					e.preventDefault();
				}
			});
	        // END Borrow Equipment Module

	        // Return Equipment Module
	        $("#returnerID").bind('keyup mouseup',function(){
	        	// console.log($(this).val().length);
	        	if($(this).val().length > 0 && $(this).val().length < 8){
	        		$("#returnerName").val('');
	        		$("#returnedEquipments").html('<tr><td>Validating ID number...</td><td></td></tr>');
	        		$(".idNumValidate").text('Field must be 8 characters.');
	        		$('.idNumCheck').removeClass("fa fa-check");
	        		$('.nameCheck').removeClass("fa fa-check");
	        	}else if($(this).val().length > 8){
	        		$("#returnerName").val('');
	        		$("#returnedEquipments").html('<tr><td>Validating ID number...</td><td></td></tr>');
	        		$(".idNumValidate").text('Field length too long.');
	        		$('.idNumCheck').removeClass("fa fa-check");
	        		$('.nameCheck').removeClass("fa fa-check");
	        	}else if(0 == $(this).val().length){
	        		$("#returnedEquipments").html('<tr><td>No Records to display...</td><td></td></tr>');
	        		$(".idNumValidate").text('');
	        		$('.idNumCheck').removeClass("fa fa-check");
	        		$('.nameCheck').removeClass("fa fa-check");
	        	}else{
	        		$(".idNumValidate").text('');
	        		$('.idNumCheck').addClass("fa fa-check");
	        		$("#returnedEquipments").html('<span id="loadSpinner" style="margin-left: 220px;"><i class="fa fa-spinner fa-spin fa-5x fa-fw"></i></span><br><span style="margin-left: 170px;">Checking borrowed equipments...</span>');
					// alert($("#currLab").val())
					$.ajax({
						url:"<?php echo site_url('BorrowList/getBorrowedEquipments');?>",
						type: 'POST',
						data: {'borrower': $(this).val(),
						'labID':  $("#currLab").val()},
						dataType: 'json',
						success: function(data){
							if(data[0].length != 0){
								$("#returnerName").val(data[0][0].studentName);
								$('.nameCheck').addClass("fa fa-check");

								if(data[1].length != 0){
									$("#returnModalHeader").html(	
										'<th style="padding-right: 140px;">All Equipments</th><th style="padding-right: 147px;">Borrowed Date</th><th><input type="checkbox" class="returnAll" onclick = "checkAllReturn()"></th>');
									var returnEqp = '';
									for(var i = 0; i < data[1].length; i++){
										returnEqp += "<tr id='"+(data[1][i].eqpSerialNum || data[1][i].compSerialNum)+"'>";
										returnEqp += "<td style='padding-right: 60px;'>"+(data[1][i].eqpSerialNum || data[1][i].compSerialNum)+" - "+(data[1][i].eqpName || data[1][i].compName)+"</td>";
										returnEqp += "<td style='padding-left: 70px; padding-right: 130px;'>"+data[1][i].borrowedDate+"</td>";
										returnEqp += "<td><input type='checkbox' class='returnBoxCheck' onclick='clearReturnAll()' id='"+(data[1][i].eqpSerialNum || data[1][i].compSerialNum)+"' value='"+(data[1][i].eqpSerialNum || data[1][i].compSerialNum)+"'></td>";
										returnEqp += "</tr>";
									}
									$("#returnedEquipments").html(returnEqp);	
								}else{
									$("#returnedEquipments").html('<tr><td>No Borrowed Equipment(s)...</td><td></td></tr>');
								}
							}else{
								$("#returnerName").val('');
								$('.nameCheck').removeClass("fa fa-check");
								$("#returnedEquipments").html('<tr><td>No Records to Display...</td><td></td></tr>');
							}
						}
					});
				}
			});

	        $("#returnBtn").click(function(){
	        	var returnItems = document.getElementById('returnedEquipments').getElementsByClassName('returnBoxCheck');
	        	var returnItemsArray = [];
	        	var returnEqps = '';
	        	for(var i = 0; i < returnItems.length; i++){
	        		if($("#returnedEquipments #"+returnItems[i].id).is(':checked')){
	        			returnItemsArray.push(returnItems[i].id);
	        			returnEqps += '<span style="font-weight: bold;">'+$("#returnedEquipments #"+returnItems[i].id+" td")[0].textContent+'</span><br>';
	        		}
	        	}
	        	if(returnItemsArray.length != 0){
	        		$.ajax({
	        			url: "<?php echo site_url('BorrowList/returnEquipments');?>",
	        			type: 'POST',
	        			data: {'equipment': returnItemsArray},
	        			success: function(data){
	        				$.ajax({
	        					url: "<?php echo site_url('Reports/storeLog');?>",
	        					type: 'POST',
	        					data: {
	        						'studentID': $("#returnerID").val(),
	        						'equipment': returnItemsArray,
	        						'action': 'return',
	        						'labID': $("#currLab").val()
	        					},
	        					success: function(data){
	        					}
	        				});

	        				$("#returnModal").modal('hide');
	        				$("#notifyModal").modal('show');
	        				$("#notifyModal .notifyHeader").html('Successfully Returned Equipment(s)');
	        				$("#notifyModal #divContent").html(returnEqps);  
	        				$("#notifyModal").on('hidden.bs.modal', function (e) {
	        					location.reload();
	        				});
	        			}
	        		});  
	        	}else{
	        		//alert('Choose equipment(s)...');
	        		bootbox.alert({
							    message: "<p align='center'>Choose equipment(s)...</p>",
							    backdrop: true
					});
	        	}
	        });
	        //END Return Equipment Module

	        //Repair Equipment

	        $("#repair").click(function(){
	        	$.ajax({
	        		url: "<?php echo site_url('DamageList/getDamageEquipments');?>",
	        		type: 'POST',
	        		data: {'labID': $("#currLab").val()},
	        		dataType: 'json',
	        		success: function(data){
	        			console.log(data);
	        			if(data.length != 0){
	        				var repairEqp = '';
	        				for(var i = 0; i < data.length; i++){
	        					repairEqp += "<tr id='"+(data[i].eqpSerialNum || data[i].compSerialNum)+"'>";
	        					repairEqp += "<td>"+(data[i].eqpSerialNum || data[i].compSerialNum)+" - "+(data[i].eqpName || data[i].compName)+"</td>";
	        					repairEqp += "<td>"+data[i].dateReported+"</td>";
	        					repairEqp += "<td><input type='checkbox' class='repairBoxCheck' onclick='clearRepairAll()' id='"+(data[i].eqpSerialNum || data[i].compSerialNum)+"' value='"+(data[i].eqpSerialNum || data[i].compSerialNum)+"'></td>";
	        					repairEqp += "</tr>";
	        				}
	        				$("#repairEquipments").html('<tr class="th"><td>Equipment</td><td >Date Reported</td><td><input type="checkbox" class="repairAll" onclick = "checkAllRepair()"></td></tr>'+repairEqp);	
	        			}else{
	        				$("#repairEquipments").html('<tr><td>No Damaged Equipment(s)...</td></tr>');
	        			}
	        		}
	        	});
	        });
	        

	        $("#repairBtn").click(function(){
	        	var repairItems = document.getElementById('repairEquipments').getElementsByClassName('repairBoxCheck');
	        	var repairItemsArray = [];
	        	var repairEqps = '';
	        	for(var i = 0; i < repairItems.length; i++){
	        		if($("#repairEquipments #"+repairItems[i].id).is(':checked')){
	        			repairItemsArray.push(repairItems[i].id);
	        			repairEqps += '<span style="font-weight: bold;">'+$("#repairEquipments #"+repairItems[i].id+" td")[0].textContent+'</span><br>';
	        		}
	        	}
	        	if(repairItemsArray.length != 0){
	        		$.ajax({
	        			url: "<?php echo site_url('DamageList/repairEquipments');?>",
	        			type: 'POST',
	        			data: {'equipment': repairItemsArray},
	        			success: function(data){
	        				$.ajax({
	        					url: "<?php echo site_url('Reports/storeLog');?>",
	        					type: 'POST',
	        					data: {
	        						'studentID': '0',
	        						'equipment': repairItemsArray,
	        						'action': 'repair',
	        						'labID': $("#currLab").val()
	        					},
	        					success: function(data){
	        					}
	        				});

	        				$("#repairModal").modal('hide');
	        				$("#notifyModal").modal('show');
	        				$("#notifyModal .notifyHeader").html('Successfully Repaired Equipment(s)');
	        				$("#notifyModal #divContent").html(repairEqps);  
	        				$("#notifyModal").on('hidden.bs.modal', function (e) {
	        					location.reload();
	        				});
	        			}
	        		});  
	        	}else{
	        		//alert('Choose equipment(s)...');
	        		bootbox.alert({
							    message: "<p align='center'>Choose equipment(s)...</p>",
							    backdrop: true
					});
	        	}
	        });		
			//END Repair Equipment Module	
		});

		// (added by JV)
		var totalPrice = 0;

		// modal reset module
		$(document).on('hidden.bs.modal', function (e) {
			$(".idNumValidate, .nameValidate, .teacherValidate, .inchargeValidate").text('');

			// damage modal reset
			$("#damageModal").find("input,textarea,select").val('');
			$('input[class=boxCheckDamage]').prop('checked', false);
			$("#damagedEquipList .damageItem").attr('checked', false);
			if(equipListLoad.length != 0){
				$("#damagedList").html(equipListLoad);
			}
			$("#damagedEquipments").html('');
			$("#price").html(0);
			totalPrice = 0;
			damagedEquipmentsArray = [];
			$('.idNumCheck').removeClass("fa fa-check");
			$('.nameCheck').removeClass("fa fa-check");
			$('.teacherCheck').removeClass("fa fa-check");
			$("#damagerName").attr('disabled', false);
			$("#damagerTeacher").attr('disabled', false);

	        // borrow modal reset
	        $("#borrowModal").find("input,textarea,select").val('');
	        $('input[class=boxCheck]').prop('checked', false);
	        $("#borrowedEquipList .returnItem").attr('checked', false);
	        if(borrowedEquipListLoad.length != 0){
	        	$("#borrowedList").html(borrowedEquipListLoad);
	        }
	        $("#borrowedEquipments").html('');
	        $("#borrowedPrice").html(0);
	        borrowedEquipmentsArray = [];
	        $('.idNumCheck').removeClass("fa fa-check");
	        $('.nameCheck').removeClass("fa fa-check");
	        $('.teacherCheck').removeClass("fa fa-check");
	        $("#borrowerName").attr('disabled', false);
	        $("#borrowerTeacher").attr('disabled', false);

	        // return modal reset
	        $("#returnModal").find("input,textarea,select").val('');
	        $('input[class=returnBoxCheck]').prop('checked', false);
	        $("#returnedEquipments .returnAll").attr('checked', false);
	        $("#returnedEquipments").html('<tr><td>No Records to display...</td><td></td></tr>');
	        $('.idNumCheck').removeClass("fa fa-check");
	        $('.nameCheck').removeClass("fa fa-check");

	    	 // repair modal reset
	    	 $('input[class=repairBoxCheck]').prop('checked', false);
	    	 $("#repairEquipments .repairAll").attr('checked', false);

		     // edit equipment modal reset
		     $("#editModal").find("input,textarea,select").val('');

		     $("#addEqpmnt").find("input,textarea,select").val('');
		     $("#addEqpmnt").find("input[value=equipment]").prop('checked', 'checked');

	         // add lab modal reset
	         $("#labName, #description").val('');
	     });

		
		function checkDamage(thisValue, thisID){
			console.log('checkDamage');
			var getValue = thisValue.value.split("-");
			var id = getValue[0]+"id";
			console.log(getValue[0]+" "+getValue[0]);
			if($("#damagedEquipList #"+getValue[0]).is(':checked')){
				damagedEquipmentsArray.push(getValue[0]);
				var newDamage = "<tr id ="+id+" class='damagedListClass'>";
				newDamage +="<td style='width: 100%'>"+getValue[0]+" "+getValue[1]+"</td>";
				newDamage +="<td>"+getValue[2]+"</td>";
				newDamage +="</td>";
				totalPrice += parseInt(getValue[2]);    

				$("#damagedEquipments").append(newDamage);
			}
			else{
				damagedEquipmentsArray.splice(damagedEquipmentsArray.indexOf(getValue[0]), 1);
				if($("#damagedEquipList .damageItem").is(':checked')){
					$("#damagedEquipList .damageItem").prop('checked', false);
				}
				$("#"+id).remove();
				totalPrice -= parseInt(getValue[2]);
			}
			$("#price").html(totalPrice);
		}

		function checkBorrow(thisValue, thisID){
			var getValue = thisValue.value.split("-");
			var id = thisID+"id";
			console.log(getValue[0]+" "+getValue[0]);
			if($("#borrowedEquipList #"+getValue[0]).is(':checked')){
				borrowedEquipmentsArray.push(getValue[0]);
				console.log(borrowedEquipmentsArray);
				var newDamage = "<tr id ="+id+" class='borrowedListClass'>";
				newDamage +="<td style='width: 100%'>"+getValue[0]+" "+getValue[1]+"</td>";
				newDamage +="</td>";
				
				$("#borrowedEquipments").append(newDamage);
			}
			else{
				console.log(borrowedEquipmentsArray.indexOf(getValue[0]));
				borrowedEquipmentsArray.splice(borrowedEquipmentsArray.indexOf(getValue[0]), 1);
				console.log(borrowedEquipmentsArray);
				if($("#borrowedEquipList .returnItem").is(':checked')){
					$("#borrowedEquipList .returnItem").prop('checked', false);
				}
				$("#"+id).remove();
			}
		}

		function checkAllDamage(){
			var table = document.getElementById('damagedList');
			var items = table.getElementsByClassName('boxCheckDamage');

			if($("#damagedEquipList .damageItem").is(':checked')){
				var list = document.getElementById('damagedEquipments').getElementsByClassName('damagedListClass');
				console.log('length', list.length);
				for (var i = 0; i < items.length; i++) {
					var item = items[i].value.split("-");
					var id = item[0]+"id";
					var checkExists = false;
					damagedEquipmentsArray.push(parseInt(item[0]));
					if(list.length != 0){
						for(var j = 0; j < list.length; j++){
							if(id == list[j].id){
								checkExists = true;
								break;
							}
						}
						if(false == checkExists){
							var newDamage = "<tr id ="+id+" class='damagedListClass'>";
							newDamage +="<td style='width: 100%'>"+item[0]+" "+item[1]+"</td>";
							newDamage +="<td>"+item[2]+"</td>";
							newDamage +="</td>";

							$("#damagedEquipments").append(newDamage);
							totalPrice += parseInt(item[2]);
						}
					}else{
						var newDamage = "<tr id ="+id+" class='damagedListClass'>";
						newDamage +="<td style='width: 100%'>"+item[0]+" "+item[1]+"</td>";
						newDamage +="<td>"+item[2]+"</td>";
						newDamage +="</td>";

						$("#damagedEquipments").append(newDamage);
						totalPrice += parseInt(item[2]);    
					}
					$(".boxCheckDamage").prop('checked', true);
				}
			}
			else{
				damagedEquipmentsArray = [];
				for (var i = 0; i < items.length; i++) {
					$(".boxCheckDamage").prop('checked', false);
				}
				$("#damagedEquipments").html('');
				totalPrice = 0;
			}
			$("#price").html(totalPrice);
		}

		function checkAllBorrow(){
			var table = document.getElementById('borrowedList');
			var items = table.getElementsByClassName('boxCheck');

			if($("#borrowedEquipList .returnItem").is(':checked')){
				var list = document.getElementById('borrowedEquipments').getElementsByClassName('borrowedListClass');
				console.log('length', list.length);
				for (var i = 0; i < items.length; i++) {
					var item = items[i].value.split("-");
					var id = item[0]+"id";
					var checkExists = false;
					borrowedEquipmentsArray.push(parseInt(item[0]));
					console.log(borrowedEquipmentsArray);
					if(list.length != 0){
						for(var j = 0; j < list.length; j++){
							if(id == list[j].id){
								checkExists = true;
								break;
							}
						}
						if(false == checkExists){
							var newDamage = "<tr id ="+id+" class='borrowedListClass'>";
							newDamage +="<td style='width: 100%'>"+item[0]+" "+item[1]+"</td>";
							newDamage +="</td>";

							$("#borrowedEquipments").append(newDamage);
							totalPrice += parseInt(item[2]);
						}
					}else{
						var newDamage = "<tr id ="+id+" class='borrowedListClass'>";
						newDamage +="<td style='width: 100%'>"+item[0]+" "+item[1]+"</td>";
						newDamage +="</td>";

						$("#borrowedEquipments").append(newDamage);  
					}
					$(".boxCheck").prop('checked', true);
				}
			}
			else{
				borrowedEquipmentsArray = [];
				for (var i = 0; i < items.length; i++) {
					$(".boxCheck").prop('checked', false);
				}
				$("#borrowedEquipments").html('');
				
			}
			$("#borrowedPrice").html(totalPrice);
		}

		function viewEquipmentHistory(viewThisEquipment, thisName){
			$("#vehModal").modal('show');
			$("#vehModal .modal-title").html(viewThisEquipment+" - "+thisName);
			$.ajax({
				url: "<?php echo site_url('Equipment/getEquipmentHistory');?>",
				type: 'POST',
				data: {'equipmentSerialNum': viewThisEquipment},
				success: function(data){
					console.log('equipmentHistory', data);
					var history = '';
					var action = '';
					if(data.length != 0){
						for(var i = 0; i < data.length; i++){
							console.log(data[i].action)
							history += "<tr>";
							history += "<td>"+data[i].date+"</td>";
							switch(data[i].action){
								case "borrow": action += "<td>Borrowed by:<br>Student ID: "+data[i].studentID+"<br>Student Name: "+data[i].studentName+"</td>"; break;
								case "return": action += "<td>Returned by:<br>Student ID: "+data[i].studentID+"<br>Student Name: "+data[i].studentName+"</td>"; break;
								case "damage": action += "<td>Filed as damage by:<br>Student ID: "+data[i].studentID+"<br>Student Name: "+data[i].studentName+"</td>"; break;
								case "repair": action += "<td>Repaired</td>"; break;
								case "move": action += "<td>Moved to: "+data[i].labID+"</td>"; break;
								case "add": action += "<td>Added to: "+data[i].labID+"</td>"; break;
								case "edit": action += "<td>Edited</td>"; break;
							}
							history += action;
							history += "</tr>";
							action = '';
						}
					}
					$("#equipmentHistory").html(history);
					if(data.length == 0){
						$("#equipmentHistory").html("<tr><td>No records to display...</td><td></td></tr>");
					}
				}
			});  
			$("#equipmentHistory").html('<tr><td><span id="loadSpinner" style="margin-left: 220px;"><i class="fa fa-spinner fa-spin fa-5x fa-fw"></i></span></td></tr>');
		}

		function editEquipment(editThisEquipment){
			$("#editModal").modal('show');
			$.ajax({
				url: "<?php echo site_url('Equipment/getEquipmentDetails');?>",
				type: 'POST',
				data: {'equipmentSerialNum': editThisEquipment},
				success: function(data){
					console.log(data[0]);
					if(data.length != 0){
						$("#editSerialNum").val(data[0].serialNum);
						$("#editName").val(data[0].name);
						$("#editPrice").val(data[0].price);
					}
				}
			});  
		}

		function checkAllReturn(){
			var table = document.getElementById('returnedEquipments');
			var items = table.getElementsByClassName('returnBoxCheck');

			if($("#returnModalHeader .returnAll").is(':checked')){
				for(var i = 0; i < items.length; i++){
					$(".returnBoxCheck").prop('checked', true);
				}
			}else{
				for(var i = 0; i < items.length; i++){
					$(".returnBoxCheck").prop('checked', false);
				}
			}
		}

		
		function clearReturnAll(){
			if($("#returnModalHeader .returnAll").is(':checked')){
				$("#returnModalHeader .returnAll").prop('checked', false);
			}
		}

		function checkAllRepair(){
			var table = document.getElementById('repairEquipments');
			var items = table.getElementsByClassName('repairBoxCheck');

			if($(" .repairAll").is(':checked')){
				for(var i = 0; i < items.length; i++){
					$(".repairBoxCheck").prop('checked', true);
				}
			}else{
				for(var i = 0; i < items.length; i++){
					$(".repairBoxCheck").prop('checked', false);
				}
			}
		}


		function clearRepairAll(){
			if($(".repairAll").is(':checked')){
				$(".repairAll").prop('checked', false);
			}
		}

		// ID number checker module
		function checkIDnumber(thisID){
			console.log(thisID);
			if(thisID.length == 8){
				$(".idNumValidate").text('');
				$('.idNumCheck').addClass("fa fa-check");
				$.ajax({
					url: "<?php echo site_url('Student/checkIDNum');?>",
					type: 'POST',
					data: {'studentID': thisID},
					success: function(data){
						if(data.length != 0){
						    // console.log(data);
						    $("#damagerName, #borrowerName").attr('disabled', true);
						    $("#damagerName, #borrowerName").val(data[0].studentName);

						    $('.nameCheck').addClass("fa fa-check");
						}else{
							$("#damagerName, #borrowerName").attr('disabled', false);
							$("#damagerName, #borrowerName").val('');

							$('.nameCheck').removeClass("fa fa-check");
						}
					}
				});  
			}else if(thisID.length == 0){
				$(".idNumValidate").text('');
				$('.idNumCheck').removeClass("fa fa-check");
				$('.nameCheck').removeClass("fa fa-check");
			}else if(thisID.length > 8){
				$(".idNumValidate").text('Field length too long.');
				$('.idNumCheck').removeClass("fa fa-check");
				$('.nameCheck').removeClass("fa fa-check");

				$("#damagerName, #borrowerName").attr('disabled', false);
				$("#damagerName, #borrowerName").val('');

			}else{
				$(".idNumValidate").text('Field must be 8 characters.');
				$('.idNumCheck').removeClass("fa fa-check");
				$('.nameCheck').removeClass("fa fa-check");

				$("#damagerName, #borrowerName").attr('disabled', false);
				$("#damagerName, #borrowerName").val('');
			}
		}

	    // END ID number checker module


	    function validate(validateThis, event){
	    	console.log(validateThis.value.length)
	    	if(validateThis.value.length == 0){
	    		switch(validateThis.id){
	    			case "borrowerName":
	    			case "damagerName": $(".nameValidate").text('');
	    			(validateThis.value != '')? $('.nameCheck').addClass("fa fa-check"):  $('.nameCheck').removeClass("fa fa-check"); break;
	    			case "borrowerTeacher": 
	    			case "damagerTeacher": $(".teacherValidate").text('');
	    			(validateThis.value != '')? $('.teacherCheck').addClass("fa fa-check"): $('.teacherCheck').removeClass("fa fa-check"); break;
					case "incharge": $(".inchargeValidate").text('');
	    			(validateThis.value != '')? $('.inchargeCheck').addClass("fa fa-check"): $('.inchargeCheck').removeClass("fa fa-check"); break;
	    		}
	    	}else{
	    		if(event.keyCode != 9){
		    		var check =  /^[a-zA-Z ]*$/.test(validateThis.value);
			    	// console.log(check);
			    	// console.log(validateThis.value);
			    	// console.log(validateThis.value == " ")
			    	if(false == check || !validateThis.value.replace(/\s/g, '').length){
			    		switch(validateThis.id){
			    			case "borrowerName":
			    			case "damagerName": $(".nameValidate").text('Invalid character(s).');
			    			$('.nameCheck').removeClass("fa fa-check"); break;

			    			case "borrowerTeacher": 
			    			case "damagerTeacher": $(".teacherValidate").text('Invalid character(s).');
			    			$('.teacherCheck').removeClass("fa fa-check"); break;

			    			case "incharge": $(".inchargeValidate").text('Invalid character(s).');
			    			$('.inchargeCheck').removeClass("fa fa-check"); break;
			    		}
			    	}else{
			    		// console.log('value', validateThis.value);
			    		switch(validateThis.id){
			    			case "borrowerName":
			    			case "damagerName": $(".nameValidate").text('');
			    			(validateThis.value != '')? $('.nameCheck').addClass("fa fa-check"):  $('.nameCheck').removeClass("fa fa-check"); break;
			    			case "borrowerTeacher": 
			    			case "damagerTeacher": $(".teacherValidate").text('');
			    			(validateThis.value != '')? $('.teacherCheck').addClass("fa fa-check"): $('.teacherCheck').removeClass("fa fa-check"); break;
							case "incharge": $(".inchargeValidate").text('');
			    			(validateThis.value != '')? $('.inchargeCheck').addClass("fa fa-check"): $('.inchargeCheck').removeClass("fa fa-check"); break;
			    		}
			    	}
		   		}
	    	}
		}

		function thisLab(labID){
			currentLab = labID;
			console.log("this lab:" +currentLab);
			$("#all").removeClass("active");
			$(".lab").removeClass("active");
			$("#reports").removeClass("active");
			$("#"+labID).addClass("active");
			$("#addBtn").hide();			
			// $("#addBtn").text("Add Equipment");
			var source = "<?php echo site_url('Index/loadIframe/lab/');?>";
			var url = source+labID;
			$("#frame").attr('src', url);
		}

		function showReport(lab){
			if(lab == 'all'){
				var url = "<?php echo site_url('Reports/loadReports/all');?>";
			}else{
				var source = "<?php echo site_url('Reports/loadReports/');?>";
				var url = source+lab;
			}
			$("#labReportFrame").attr('src', url);
		}

		var itemsToMove = '';
		var numItems = 0;
		var itemsToMoveList = [];

		function moveAll(option){
			var table = document.getElementById("labEquipmentsTable").getElementsByClassName('itemDetails'); 
			
			if(option == 'all'){
				itemsToMove = '';
				numItems = 0;
				if($("#moveAll").is(':checked')){
					$(".equipCheck").prop('checked', true);
					for(var i = 0; i < table.length; i++){
						if($("#labEquipmentsTable tbody .itemDetails")[i].getElementsByTagName('input').length == 1){
							itemsToMoveList.push(table[i].children[0].textContent);
							itemsToMove += table[i].children[0].textContent+' - '+table[i].children[1].textContent;
							itemsToMove += '<br>';
							numItems++;
						}						
					}
					// $("#moveItemList").html(numItems+' item(s)');
					// $("#moveItemList").attr('title', itemsToMove);
					$("#viewItems").prop("disabled", false).html("View");
				}else{
					$(".equipCheck").prop('checked', false);
					numItems = 0;
					itemsToMove = '';
					itemsToMoveList = [];
					// $("#moveItemList").html('No item(s)');
					// $("#moveItemList").removeAttr( "title" );
					$("#viewItems").prop("disabled", true).html("No items");
				}
			}else{
				var id = option.replace('checkbox', 'tr');
				if($("#"+option).is(':checked')){
					itemsToMoveList.push($("#"+id)[0].children[0].textContent);
					itemsToMove += $("#"+id)[0].children[0].textContent+' - '+$("#"+id)[0].children[1].textContent;
					itemsToMove += '<br>';
					numItems++;

					// $("#moveItemList").html(numItems+' item(s)');
					// $("#moveItemList").attr('title', itemsToMove);
					$("#viewItems").prop("disabled", false).html("View");
				}else{
					$("#moveAll").prop('checked', false);
					numItems--;
					numItems = (numItems <= 0)? 0: numItems;
					if(numItems == 0){
						itemsToMoveList = [];
						// $("#moveItemList").html('No item(s)');
						// $("#moveItemList").removeAttr( "title" );
						itemsToMove = '';
						$("#viewItems").prop("disabled", true).html("No items");
					}else{
						itemsToMoveList.splice(itemsToMoveList.indexOf($("#"+id)[0].children[0].textContent), 1);
						var remove = $("#"+id)[0].children[0].textContent+' - '+$("#"+id)[0].children[1].textContent+'<br>';
						itemsToMove = itemsToMove.replace(remove, '');
						// $("#moveItemList").html(numItems+' item(s)');
						// $("#moveItemList").attr('title', itemsToMove);
						$("#viewItems").prop("disabled", false).html("View");
					}
				}
			}
		}

		function showBorrowed(){
			bootbox.alert({
					message: "<p align='center'>Item(s) to move: <br><br>"+itemsToMove+"</p>",
					backdrop: true
			});	 
		}

		function clearError(){
			$("#moveValidate").empty();
		}

		function moveEquipments(){
	    	// console.log(itemsToMoveList); 	
	    	if(numItems==0){
	    		//alert("There are no items to move.");
	    		bootbox.alert({
							    message: "<p align='center'>There are no items to move.</p>",
							    backdrop: true
				});
	    	}else{
	    		if(!$("#moveLabList").val()){
	    			$("#moveValidate").html("Choose a laboratory");
	    		}else{
	    			$.ajax({
	    				url: "<?php echo site_url('Equipment/moveItems');?>",
	    				type: 'POST',
	    				data: {	'newLab': $("#moveLabList").val(),
	    				'items': itemsToMoveList},
	    				success: function(data){
	    					if(data){
	    						$.ajax({
	    							url: "<?php echo site_url('Reports/storeLog');?>",
	    							type: 'POST',
	    							data: {
	    								'studentID': '0',
	    								'equipment': itemsToMoveList,
	    								'action': 'move',
	    								'labID': $("#moveLabList").val()
	    							},
	    							success: function(data){
	    							}
	    						});									
	    						//alert('Item(s) moved..');
	    						bootbox.alert({
							    	message: "<p align='center'>Item(s) moved..</p>",
							    	backdrop: true
								});
	    						location.reload();
	    					}
	    				}
	    			}); 
	    		}
	    	} 
	    }

	    function checkModalContent(thisModal){
	    	var modalID = thisModal.parents('.modal')[0].id;
	    	var content = $("#"+modalID).find("input[type=text],textarea");
	    	var close = false;
	    	for(var i = 0; i < content.length; i++){
	    		if(content[i].value.length != 0){
	    			close = true;
	    			break;
	    		}
	    	}
	    	if(close == true){
	    		if(confirm("Some data is present in the modal. Close anyway?")){
	    				$("#"+modalID).find(".close, .modal-footer .btn-danger").attr("data-dismiss" , "modal");
	    		}else{
	    			$("#"+modalID).find(".close, .modal-footer .btn-danger").removeAttr("data-dismiss" , "modal");
	    		}
	    	}else{
	    		$("#"+modalID).find(".close, .modal-footer .btn-danger").attr("data-dismiss" , "modal");
	    	}
	    }
	</script>	
</head>
<body></body>
</html>	
