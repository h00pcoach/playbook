/*
Copyright 2013 Acetrik. All rights reserved.
Visit us at http://www.acetrik.com/
*/
// JavaScript Document
var drgg='',kin=false,doc=false,dragging=false,d;
var stage;
var moves=new Array();
var filenm='Name',fileid=0;
var tags=['Arrow','Player','Ball','Forward','Cone'];
var layer;
var halfcourt=false;
var hash=0;
    var touch=!!('ontouchstart' in window);
var saved=true;

function connecting(ele)
{
	$(ele).animate({opacity:.5},500,function()
	{
		if($(ele).next('img').length>0)
			connecting($(this).next());
		else
			connecting('#load>img:first');
		$(this).animate({opacity:1},500,function(){ $(this).animate({opacity:0},900);});
	});
}

function showLoading(show)
{
	// console.log('showLoading: ', show);
	if(!show){
		connecting('#load>img:first');
		$('#large').show();
		$('#loading').show();
	}
	else{
		$('#load>img').stop().css({'opacity':0});
		$('#large').hide();
		$('#loading').hide();
	}
}

function loadGame()
{
	var nm=location.href.split('?')[1];
	if(nm){
		var userid=user.id;
		if(nm.split('&').length>1){
			userid=nm.split('&')[1].split('=')[1];
			nm=nm.split('&')[0].split('=')[1];
		}
		else
			nm=nm.split('=')[1];
		if(userid==''){
			alert('Kindly login again to proceed.');
			loadElements();
			filenm='Name';
			fileid='';
			$('#userid').val(user.id);
			$('#id').val('');
			return false;
		}
		showLoading();
		$.ajax({
			url:'load.php',//nm,
			data:{id:nm,user_id:userid},
			dataType:'json',
			success: function(data){
				halfcourt=(data.play.halfcourt=="true"?true:false);
				hash=data.play.skin;
				moves=data.play.frames;
				filenm=data.play.name;
				fileid=data.id;
				$('#userid').val(userid);
				user.uped=data.uped;
				user.downed=data.downed;
				$('#id').val(data.id);
				$('#tags').val(data.tags);
				$('#speed').val(data.play.speed);
				$('#moves').html('');
				for(var i in moves)
				{
					$('#moves').append($('<option>', {
						value: i,
						text: moves[i].name
					}));
				}
				var $skin=($('skin#'+hash,d));
				showLoading(true);
				loadElements(true);
				$('#court').val(halfcourt?1:0);
				/*if(!halfcourt){
					$('#container').css({'height':'509px','padding-top':'0px'});
					stage.setHeight(509);
					setBackground($('court>imagefull',$skin).text(),509);
				}
				else{
					$('#container').css({'height':'250px','padding-top':'250px'});
					stage.setHeight(250);
					setBackground('Model/Frame01.png',250);
				}*/
			},
			error: function(d){
				alert('Your internet connection is lost or the server encountered an error.');
				loadElements();
				filenm='Name';
				fileid='';
				$('#userid').val(user.id);
				$('#id').val('');
			}
		});
	}
	else{
		loadElements();
		filenm='Name';
		fileid='';
		$('#userid').val(user.id);
		$('#id').val('');
	}
}
function changeSkin()
{
	// console.log('changeSkin!');
	var $skin=($('skin#'+hash,d));
	$('title').html('Simple '+$('name',$skin).text()+' Plays and '+$('name',$skin).text()+' Drills Software');
	if($('#court').val()==0)
	{
		$('#container').css({'height':'509px','padding-top':'0px'});
		setBackground($('court>imagefull',$skin).text(),509);
		halfcourt = false;
	}
	else {
		// $('#container').css({'height':'250px','padding-top':'250px'});
		$('#container').css({'height':'255px','padding-top':'255px'});
		$('.kineticjs-content').css({'height':'255px'});

		setBackground('Model/Img/pb_court_half.jpg',255);
		halfcourt=true;
	}
	// $('#Ball>ul').html('');
	// $('balls',$skin).children().each(function(i,e)
	// {
	// 	$('#Ball>ul').append('<li><img src="'+$(this).text()+'" id="b'+i+'"/></li>');
	// });

}
function loadElements(l){
	showLoading();
	$.ajax({
		url:'system_playbook_test.xml',
		dataType:'xml',
		success: function(data){
			d=data;
			stage = new Kinetic.Stage({
			  container: 'container',
			  width: 306,
			  height: 509
			});
			layer = new Kinetic.Layer();
			changeSkin();
			if(!l){
				var m={name:'Movement 1',comment:'',frame:new Array()};
				moves.push(m);
				$('#moves').html($('<option>', {
					value: 0,
					text: 'Movement 1',
					selected:'selected'
				}));
			}
			$('#moves').val(0);
			//$('#comment').val('');
			$('#moves').trigger('change');
			showLoading(true);
		}
	});
}
var loadingNow=false, loadinItems='';
function loadLayer()
{
//	console.log('LoadLayer! ');
	if(playLayer && playLayer.children) {playLayer.destroy(); playLayer='';}
	layer.setVisible(true);
	layer.destroyChildren();
	grp=new Array();
	layer.drawScene();
	loadingNow=true;
	var sel=$('#moves').val();
	glob='';
	if(!moves[sel].frame || moves[sel].frame.length<1){
		loadinItems=0;
		loadingNow=false;
		$('#comment').val(moves[sel].comment);
		return;
	}
	$('#comment').val(moves[sel].comment);
	loadinItems=moves[sel].frame.length;
	for(var i=0;i<moves[sel].frame.length;++i){
		var v=moves[sel].frame[i];
		if(v.category=='Arrow'){
			if(parseInt(v.type)!=5){
				switch(parseInt(v.type)){
					case 1:
							break;
					case 2:
							pass=true;
							break;
					case 3:
							jogged=true;
							break;
					case 4:
							arrow=false;
							break;
				}
				grp.push(new Kinetic.Group({
					draggable:true,
					id:v.id
				}));
				var ln=grp.length-1;
				line = new Kinetic.Line({
					points: [parseFloat(v.points[0].x), parseFloat(v.points[0].y), parseFloat(v.points[1].x), parseFloat(v.points[1].y)],
					stroke: "black",
					strokeWidth: 2,
					dashArray:[10,5],
					dashArrayEnabled:pass
				});
				autoLine=true;
				grp[ln].add(line);
				draw_Lines(parseFloat(v.points[1].x), parseFloat(v.points[1].y,ln));
				grp[ln].setAbsolutePosition(parseFloat(v.x),parseFloat(v.y));
				layer.drawScene();
			}
			else{
				grp.push(new Kinetic.Group({
					draggable:true,
					id:v.id
				}));
				for(var i in v.points)
					s_points.push({x:parseFloat(v.points[i].x),y:parseFloat(v.points[i].y)});
				autoLine=true;
				drawSpline();
				var ln=grp.length-1;
				grp[ln].setAbsolutePosition(parseFloat(v.x),parseFloat(v.y));
				layer.drawScene();
			}
		}
		else
			autoDrop(parseFloat(v.x),parseFloat(v.y),$('#'+v.id).attr('src'),$('#'+v.id).width(),$('#'+v.id).height(),v.id,true);
	}
}
var playLayer,tweens=new Array();
function playLoading(v,frm,to,lst){
	var imageObj = new Image();
	imageObj.onload = function(){
		var image = new Kinetic.Image({
			x: parseFloat(frm.x),
			y: parseFloat(frm.y),
			image: imageObj,
			width: $('#'+v.id).width(),
			height: $('#'+v.id).height()
		 });

		var gr=(new Kinetic.Group({
			x:parseFloat(frm.x),
			y:parseFloat(frm.y),
			draggable:true,
			offset:[frm.x,frm.y],
			id:frm.id
		}));
		gr.add(image);
		var rect=new Kinetic.Rect({
			x: parseFloat(frm.x),
			y: parseFloat(frm.y),
			fill: 'black',
			stroke: 'black',
			strokeWidth: 1,
			width:$('#'+v.id).width(),
			height:$('#'+v.id).height(),
			opacity:0
		});
		gr.add(rect);
		playLayer.add(gr);
		playLayer.drawScene();
		/*var tween = new Kinetic.Tween({
			node: gr,
			duration: 2,
			x: to.x,
			y: to.y,
			easing: Kinetic.Easings.Linear,
			onFinish: function(){
				nextFrame(this.node,lst);
			}
		});*/
		tweens.push(gr);
		plItems--;
	};
	imageObj.src = $('#'+v.id).attr('src');

//	console.log('imageObj src: ', imageObj.src);
}
function nextFrame(node,lst,nxt){
	//var gr=playLayer.get('#'+id);
	if(moves.length>lst){
		playingStill=true;
		$('#moves').val(lst);
		for(var k=0;k<moves[lst].frame.length;++k){
			if(node.getId()==moves[lst].frame[k].id){
				var tween = new Kinetic.Tween({
					node: node,
					duration: parseInt($('#speed').val())/1000,
					x: parseFloat(moves[lst].frame[k].x),
					y: parseFloat(moves[lst].frame[k].y),
					easing: Kinetic.Easings.Linear,
					onFinish: function(){
						if(!nxt)
							nextFrame(this.node,lst+1);
						else
							playingStill=false;
					}
				});
				tween.play();
			}
		}
	}
	else playingStill=false;
}
var playingStill=false;
function playNow(lst,nxt)
{
//	console.log('playNow: ', lst, nxt);
	if(moves.length>1 && moves.length>lst){
			if(playLayer && playLayer.children) playLayer.remove();
			playLayer= new Kinetic.Layer();
			stage.add(playLayer);
			tweens=new Array();
			var cnt=0;
			plItems=0;
			//for(var j=0;j<moves.length-1;++j){
				var j=lst-1;
//				console.log(lst);
				for(var i=0;i<moves[j].frame.length;++i){
					var v=moves[j].frame[i];
					//lst=j+1;
					for(var k=0;k<moves[lst].frame.length;++k){
						if(moves[j].frame[i].id==moves[lst].frame[k].id){
							playLoading(v,moves[j].frame[i],moves[lst].frame[k],lst);
							plItems++;
							break;
						}
					}
				}
			//}
			playing=setInterval(function(){
				if(plItems==0){
					layer.setVisible(false);
					for(var i=0;i<tweens.length;++i){
						nextFrame(tweens[i],lst,nxt);
					}
					clearInterval(playing);
					playing='';
				}
			},300);
		}
}
var img;
function setUser(f){
	if(f){
		$('#logged>div').show();
		$('#logged>div>img').attr('src',user.avatarurl);
		$('#name').html(user.lastname);
		$('#gol').show();
		$('#log').hide();
		$('.myplay').show();
	}
	else{
		$('#logged>div').hide();
		$('#gol').hide();
		$('.myplay').hide();
		$('#log').show();
		$('#lists>table').html('');
	}
}
function saveG(nameG,old)
{
//	console.log('saveG: ', nameG, old);
	if(nameG){
		showLoading();
		img='';
		saving(nameG,old);
	}
}
function saving(nameG,old)
{
//	console.log('saving ace: ', nameG, old);
	var names='';
	var comm='';
	for(var i=0;i<moves.length;++i){
		names+='`'+moves[i].name;
		comm+='`'+moves[i].comment;
	}
	if(old){
		$.ajax({
			type:'POST',
			url:'save.php',
			data:{
				id:fileid,
				names:names,
				comment: comm,
				tags:$.trim($('#tags').val().toLowerCase()),
				data:{halfcourt:halfcourt, name:filenm,names:names,comment: comm,skin:hash,frames:moves,speed:$('#speed').val()}
			},
			dataType:'xml',
			success: function(d){
				if($('status',d).text()=='1'){
					$('#saveGame').hide('slow');
					$('#lrg').hide('slow');
					$('#savename').val('');
					$('#id').val($('id',d).text());
					loadScreens(0,$('id',d).text());
				}
				//alert($('msg',d).text());
				//showLoading(true);
			}
		});
	}
	else{
		$.ajax({
			type:'POST',
			url:'save.php',
			data:{
				uid:user.id,
				cat: $('#cat').val(),
				name:nameG,
				names:names,
				dataType:"xml",
				comment: comm,
				tags:$.trim($('#tags').val().toLowerCase()),
				data:{halfcourt:halfcourt,name:nameG,skin:hash,frames:moves,speed:$('#speed').val()}
			},
			success: function(d){
				if($('status',d).text()=='1'){
					$('#saveGame').hide('slow');
					$('#lrg').hide('slow');
					$('#savename').val('');
					$('#id').val($('id',d).text());
					loadScreens(0,$('id',d).text());
				}
				//alert($('msg',d).text());
				//showLoading(true);
			}
		});
	}
}
var lastim=1;
function loadScreens(no,imid){
	$('#moves').val(no);
	loadLayer();
	setTimeout(function(){
		stage.toDataURL({
			callback:function(d){
				img+=','+d.split(',')[1];
				no++;
				/*if(moves.length==no){
					pdff=true;
					showLoading(true);
					img=img.substr(1);
					saving(nameG,old);
					pdff=false;
				}
				else{ */
					if(no%5==0 || moves.length==no){
						$.ajax({
							type:'POST',
							url:'save.php',
							data:{
								path:imid,
								img:img.substr(1),
								no:lastim
							},
							success: function(d){
								//if($('status',d).text()=='1'){
										img='';
								lastim=no;
									if(no==moves.length){
										$('#saveGame').hide('slow');
										$('#lrg').hide('slow');
										alert('Saved successfully.');
										showLoading(true);
										lastim=1;
									}
								//}
								else
									loadScreens(no,imid);
							}
						});
					}
					else loadScreens(no,imid);
				//}
			}
		});
	},1000);
}
function pdf(no)
{
//	console.log('pdf: ', no);
	$('#moves').val(no);
	loadLayer();
	setTimeout(function(){
		stage.toDataURL({
			callback:function(d){
				pd+='<div><h1>'+moves[no].name+'</h1>';
				pd+='<img width="70%" src="'+d+'" /></div>';
				no++;
				if(moves.length==no){
					pdff=true;
					showLoading(true);
					$('body').append("<form id='pd' target='_blank' action='pdf/report.php' method=\"POST\"><input type=\"hidden\" name=\"pdf\" value='"+pd+"' /></form>");
					$('#pd').submit();
					pdff=false;
				}
				else pdf(no);
			}
		});
	},1000);
}
function loadPlay(e){
	$pl=$(e).parent().data('play');
	location.hash='?id='+$pl;
	loadGame();
	$('#plays').hide('slow');
	$('#lrg').hide('slow');
}
function remPlay(e){
	if(confirm('Are you sure you want to remove this play?')){
		$pl=$(e).parent().data('play');
		var $tr=$(e).parent().parent();
		showLoading();
		$.ajax({
			url:'save.php',
			type:'POST',
			data: {remove:true,id:$pl,uid:user.id},
			success:function(e){
				alert('Your Play has been successfully removed.');
				$tr.remove();
				showLoading(true);
			}
		});
	}
}
var xml,pdff=false,pd='';
$(document).ready(function(){
	doc=true;
	$('#comment').keyup(function(e) {
		moves[$('#moves').val()].comment=$(this).val();

    });
	$('#pd').submit(function(e) {
		var currr=$('#moves').val();
		var nm=location.href.split('?')[1];
		if(!nm && ($('#id').val()=='' || $('#id').val()=='0')){
			alert('Save the Frames first.');
			return false;
		}
		else if(nm.split('&').length>1){
			$('#user_id').val(nm.split('&')[1].split('=')[1]);
			$('#id').val(nm.split('&')[0].split('=')[1]);
			if(user.id==$('#user_id').val())
				return confirm('PDF will be generated as per last saved version. Continue?');
		}
		else{
			$('#id').val(nm.split('=')[1]);
			if(user.id!=''){
				$('#user_id').val(user.id);
				return confirm('PDF will be generated as per last saved version. Continue?');
			}
			else{
				alert('Your session has expired please login again.');
				return false;
			}
		}
		return true;
    });
	$('#new').click(function(e) {
        location.hash='';
		$('#id').val('');
		loadGame();
    });
	$('#playClose').click(function(){
		$('#plays').hide('slow');
		$('#lrg').hide('slow');
	});
	$('#cang').click(function(){
		$('#saveGame').hide('slow');
		$('#lrg').hide('slow');
	});
	$('#gol').click(function(e){
		showLoading();
		$.ajax({
			url:'logout.php',
			data:{},
			success: function(d){
				user={id:'',lastname:'',email:'',ismale:'',role:'',profileurl:'',avatarurl:'',hoopid:''};
				setUser(false);
				$('#id').val('');
				showLoading(true);
			}
		});
	});
	$('.myplay').click(function(e){
		$('#lrg').show('slow');
		$('#plays').show('slow');
	});
	$('#log').click(function(e){
		$('#lrg').show('slow');
		$('#login').show('slow');
	});
	$('#logg').click(function(e){
		showLoading();
		$.ajax({
			url:'login.php',
			data: {mail: $('#email').val(),pwd:$('#pass').val()},
			type:'POST',
			dataType:"xml",
			success: function(d){
				xml=d;
				if($('status',d).text()=='1'){
					var us=$('user',d);
					user.id=$('id',us).text();
					user.lastname=$('lastname',us).text();
					user.mail=$('email',us).text();
					user.ismale=$('ismale',us).text();
					user.role=$('role',us).text();
					user.profileurl=$('profileurl',us).text();
					user.avatarurl=$('avatarurl',us).text();
					user.hoopcoachid=$('hoopcoachid',us).text();

					if(user.avatarurl.length<5)
						if(user.ismale=='1')
							user.avatarurl='Model/Img/male_avatar_icon.png';
						else
							user.avatarurl='Model/Img/female_avatar_icon.png';
					//var cat=$('category',d);
					$('#cat').html('');
					$('category',d).each(function(i,e){
						$('#cat').append($('<option>', {
							value: $('id',this).text(),
							text: $('name',this).text()
						}));
					});
					$('#cat').val($('#cat option:first').val());
					$('#id').val('');
					setUser(true);
					$('#lrg').hide('slow');
					$('#login').hide('slow');
					$('#lists>table').html('');
					if($('play',d).length>0)
						$('play',d).each(function(i,e){
							$('#lists>table').append('<tr><td width="50%">'+$('name',this).text()+'</td><td data-play="'+$('id',this).text()+'" data-path="'+$('path',this).text()+'"><a href="play.php?id='+$('id',this).text()+'"><img src="Model/Img/play.jpg" height="19" class="loadPlay"  /></a><img src="Model/Img/del.jpg" class="remPlay" onClick="remPlay(this)" /></td></tr>');
						});
				}
				else{
					alert("Your email or password doesn't match");
					user={id:'',lastname:'',email:'',ismale:'',role:'',profileurl:'',avatarurl:'',hoopid:''};
					setUser(false);
				}
				showLoading(true);
			}
		});
	});
	$('#canc').click(function(e){
		$('#lrg').hide('slow');
		$('#login').hide('slow');
	});
	$('#play, .play-court').click(function()
	{
//		console.log('play clicked! ', $(this));
		playNow(1,false);
	});

	$('.nxtFrame').click(function(){
		playNow(parseInt($('#moves').val())+1,true);
	});
	$('#saveg').click(function(e) {
        if($.trim($('#savename').val())!='')
			saveG($.trim($('#savename').val()));
		else
			alert('Please Specify a name for the play.');
    });
	$('#save').click(function(e){
		var nm=location.href.split('?')[1];
		if(user.id=='')
		{
			alert("You'll need to register to save this game.");
			return false;
		}
		if(!nm && user.id!=''){
			$('#saveGame').show('slow');
			$('#lrg').show('slow');
		}
		else{
			var userid='';
			if(nm.split('&').length>1){
				userid=nm.split('&')[1].split('=')[1];
				nm=nm.split('&')[0].split('=')[1];
			}
			else
				nm=nm.split('=')[1];
			if(userid!='' && userid==user.id)
				saveG(nm,true);
			else
				alert("You don't have the permission to edit this Movements.");
		}
	});
	$('#moves').change(function(){ if(!playingStill) loadLayer(); });
	$('#autoadd, #autoadd_def').click(function(e)
	{

		// console.log('autoadd/autoadd_def clicked halfcourt: ', halfcourt);


		var $skin = ($('skin#'+hash, d));
		var $type = $(this).attr('data-type');
		var $element = $(this).attr('data-element');


		$('autoadd' + $type + 'button ' + $type, $skin).children().each(function(i,e)
		{
			$img = $($($element + ' img')[i]);

			var x = parseFloat($('x',this).text());
			var y = parseFloat($('y',this).text());

			// console.log('x: ', x);
			// console.log('y: ', y);
			if (halfcourt)
			{
//				console.log('halfcourt before y ==: ', y);

				// if (y < 250)
				// {
				// 	y = 10;

				// 	console.log('halfcourt y < 250: ', y);

				// }
				if (y > 250) {

					y -= 250;

//					console.log('halfcourt y > 250: ', y);
				}
			}

			// autoDrop needs x,y  src  w,h  id  loading
			autoDrop(x, y,
				$img.attr('src'),
				$img.width(), $img.height(),
				$img.attr('id'));
		});

			// Display the sixth item in the list (150 = 30*5)
//			console.log('img parent top: ', $($img).parent().parent().css('top'));
			$($img).parent().parent().css('top',parseInt($($img).parent().parent().css('top'))-150+'px');
	});

	// COURT CLICK DISMISS TOOLS MENU
	$('.clickable').click(function()
	{
//		console.log('clickable clicked: ', $(this));

		$img = $(this);


		if(stage.get('#'+$($img).attr('id'))[0])
		{
			alert('Duplicate element not allowed');
			return;
		}

		// console.log('clickable img: ', $img);

		// Grab the coords from the image
		var x= parseFloat($img.attr('data-x'));
		var y= parseFloat($img.attr('data-y'));

		if (!x)
		{
			x= 0;
		}
		if (!y)
		{
			y = 0;
		}

		// if halfcourt and y is greater that half of the canvas size subtract that amount to get the new y value
		if (halfcourt)
		{
			if (y > 250)
			{

				y -= 250;
			}
		}

		// drop the object onto the court
		autoDrop(x, y,
				$img.attr('src'),
				$img.width(),
				$img.height(),
				$img.attr('id'));
	});

	$('.next').click(function()
	{
//		console.log('next clicked! ', $(this));
		var ul=$(this).prev().children('.btn-group').children('ul');
//		console.log('next ul: ', ul);
		if(parseInt(ul.css('top'))>-ul.children('li').length*30+30)
			ul.css('top',parseInt(ul.css('top'))-30+'px');
	});
	$('.prev').click(function()
	{
//		console.log('prev clicked! ', $(this));

		var ul=$(this).next().children('.btn-group').children('ul');
		if(parseInt(ul.css('top'))<0)
			ul.css('top',parseInt(ul.css('top'))+30+'px');
	});
    $('#del_move').click(function(){
		if($('option','#moves').length>1){
			var sel=$('#moves').val();
			moves.splice(sel,1);
			sel.remove();
		}
	});
    $('#ren_move').click(function(){
		var nm=prompt('Enter name for the movement.',$('#moves option:selected').text());
		if(nm && $.trim(nm) != '')
		$('#moves option:selected').text(nm);
		moves[$('#moves').val()].name=nm;
	});
    $('#add_move').click(function(){
		var sel=$('#moves').val();
		var fr=new Array();
		for(var i=0 ; i<moves[moves.length-1].frame.length;++i){
			if(moves[moves.length-1].frame[i].category!='Arrow')
				fr.push($.extend(true, {}, moves[moves.length-1].frame[i]));
		}
		var m={name:'Movement '+(moves.length+1),frame:fr,comment:''};
		moves.push(m);
		$('#moves').append($('<option>', {
			value: moves.length-1,
			text: 'Movement '+moves.length
		}));
		$('#moves').val(moves.length-1);
		$('#moves').trigger('change');
	});
	$('#court').change(function(e)
	{
		var $skin=($('skin#'+hash,d));
        if($(this).val()==0)
        {
//       	console.log('court val is 0: ', $(this).val());
			$('#container').css({'height':'509px','padding-top':'0px'});
			stage.setHeight(509);
			setBackground($('court>imagefull',$skin).text(),509);
			if(halfcourt){
				for(var k=0;k<moves.length;++k){
					for(var j=0;j<moves[k].frame.length;++j){
						if(moves[k].frame[j].category!='Arrow'){
							//if(moves[k].frame[j].y<250){
								//var cu=moves[k].frame[j].id;

								var y = parseFloat(moves[k].frame[j].y);
								y+=255;
								moves[k].frame[j].y = y;
						}

					}
				}
				loadLayer();
			}
			halfcourt=false;
		}
		else{
//        	console.log('court val is 1: ', $(this).val());

			$('#container').css({'height':'254.5px','padding-top':'254.5px'});
			$('.kineticjs-content').css({'height':'254.5px'});

			stage.setHeight(255);
			setBackground('Model/Img/pb_court_half.jpg',255);
			if(!halfcourt){
				for(var k=0;k<moves.length;++k){
					for(var j=0;j<moves[k].frame.length;++j){
//						console.log('HALFCOURT moves y before: ', moves[k].frame[j].y);
						if(moves[k].frame[j].category!='Arrow'){
							if(moves[k].frame[j].y<255){
								moves[k].frame[j].y=10;

								/*$('#moves option').each(function(z,e){
									for(var i=0; i<moves[$(this).val()].frame.length;++i){
										var v=moves[$(this).val()].frame[i];
										if(v.id==cu)
											moves[$(this).val()].frame.splice(i,1);
									}
								});*/
							}
							else{

								moves[k].frame[j].y-=255;
							}
						}

					}
				}
				loadLayer();
			}
			halfcourt=true;
		}
    });
	$('.rate').click(function(e){
		if(parseInt(user.uped)==0 && parseInt(user.downed)==0){
			var nm=location.href.split('?')[1];
			if(nm && nm.split('&').length>1)
				nm=nm.split('&')[0].split('=')[1];
			else if(nm)
				nm=nm.split('=')[1];
			else{
				alert('Save the Movements before rating.');
				return false;
			}
			if(user.id!='' && nm){
				showLoading();
				$.ajax({
					type:'POST',
					url:'save.php',
					data:{id:nm,rate:$(this).data('rate')},
					success: function(d){
						alert(d);
						showLoading(true);
					}
				});
			}
		}
		else if(parseInt(user.uped)==-1 && parseInt(user.downed)==-1){
			alert('You\'ll need to login before rating.');
		}
	});
	$('#del').on('click',dell);
});
var glob,playing,plItems=0;
function dell(e)
{
//	console.log('dell: ', e);
	//if(dragging){
	if(glob && glob.children){
		//var sel=$('#moves').val();
		$('#moves option').each(function(i,e){
			for(var i=0; i<moves[$(this).val()].frame.length;++i){
				var v=moves[$(this).val()].frame[i];
				if(v.id==glob.getId())
					moves[$(this).val()].frame.splice(i,1);
			}
		});
		glob.destroyChildren();
		glob.destroy();
		layer.drawScene();
	}
	else{
		alert('Please Double'+(touch?' Tap ':' Click ')+'Items to select then Click Here.');
	}
	//	dragging=false;
	//}
}
function dele(e){
	stage.off('mouseover',dele);
}
function ended(e)
{
//	console.log('ended: ', e);
	drgg=false;
	document.getElementsByTagName('body')[0].style.cursor='default';
}
function initStage()
{
//	console.log('initStage: ', $(this));
	// TO ALLOW SCROLLING ON MOBILE DEVICES - BUT CAUSES ISSUES WHEN DRAGGING ELEMENTS
	// stage.on('touchstart toucheend touchmove', function(evt)
	// {
	//   evt.preventDefault = null;
	// });
		$(".btn-group img").draggable({ helper:'clone',appendTo: 'body',
		drag:function(e,u)
		{
			// console.log('drag: ', e);
			var ss=$(this);
			drgg={"src":ss.attr('src'),"height":ss.height(),"width":ss.width(),'ele':ss,'type':ss.parent().parent().parent().attr('id')};

//			console.log('drgg is what? ', drgg);
			if(playLayer && playLayer.children)  loadLayer();
		}
	});
	stage.on('mousedown',function(){ if(playLayer && playLayer.children)  loadLayer();});
	stage.on('touchdown',function(){ if(playLayer && playLayer.children)  loadLayer();});

	$( "#container canvas:last" ).droppable({
            drop: function( ev, ui ) {
				ev.preventDefault();
				if(stage.get('#'+$(drgg.ele).attr('id'))[0]){
					alert('Duplicate element not allowed');
					return;
				}
				drgg.x=$(document).scrollLeft()+ev.originalEvent.clientX-$('#container').offset().left-drgg.width/2;
				// console.log('$(document).scrollLeft(): ', $(document).scrollLeft());
				// console.log('ev.originalEvent.clientX: ', ev.originalEvent.clientX);
				// console.log('$(container).offset().left: ', $('#container').offset().left);
				// console.log('drgg.width/2: ', drgg.width/2);
				// console.log('drgg.x: ', drgg.x);
				drgg.y=$(document).scrollTop()+ev.originalEvent.clientY-$('#container').offset().top-drgg.height/2-(halfcourt?250:0);
				// console.log('drgg.y: ', drgg.y);
				drgg.id=$(drgg.ele).attr('id');
				drgg.tag=$(drgg.ele).parent().parent().parent().attr('id');
				newDrop();
				var nd=-1;
				// if($(drgg.ele).parent().next().length==0)
					// $(drgg.ele).parent().parent().css('top',parseInt($(drgg.ele).parent().parent().css('top'))+30+'px');
				// else
					// $(drgg.ele).parent().parent().css('top',parseInt($(drgg.ele).parent().parent().css('top'))-30+'px');
				$(ui).remove();
            }
          });
	//moves.push({});
}
var autoLine=false;
function autoDrop(x,y,src,w,h,id,loading)
{
//	console.log('autoDrop: ', x, y, src, w, h, id, loading);

    var imageObj = new Image();

	var sh=stage.get('#'+id)[0];
	if(sh)
	{
		sh.destroy();
	}
    imageObj.onload = function(){
        var image = new Kinetic.Image({
            x: x,
            y: y,
            image: imageObj,
            width: w,
            height: h
         });

		grp.push(new Kinetic.Group({
			x:x,
			y:y,
			draggable:true,
			offset:[x,y],
			id:id
		}));
		grp[grp.length-1].add(image);
		var rect=new Kinetic.Rect({
			x: x,
			y: y,
			fill: 'black',
			stroke: 'black',
			strokeWidth: 1,
			width:w,
			height:h,
			opacity:0
		});
		grp[grp.length-1].add(rect);
		if(loading)
			addObjects(grp[grp.length-1],false);
		else
			addObjects(grp[grp.length-1],'Player');
     };
     imageObj.src = src;
}
function newDrop()
{
	// console.log('newDrop! acetrick!');
    var imageObj = new Image();
    imageObj.onload = function(){
        var image = new Kinetic.Image({
            x: drgg.x,
            y: drgg.y,
            image: imageObj,
            width: drgg.width,
            height: drgg.height
         });

		grp.push(new Kinetic.Group({
            x: drgg.x,
            y: drgg.y,
			draggable:true,
			offset:[drgg.x,drgg.y],
			id:drgg.id
		}));
		grp[grp.length-1].add(image);
		var rect=new Kinetic.Rect({
			x: drgg.x,
			y: drgg.y,
			fill: 'black',
			stroke: 'black',
			strokeWidth: 1,
			width:drgg.width,
			height:drgg.height,
			opacity:0
		});
		grp[grp.length-1].add(rect);
		addObjects(grp[grp.length-1],drgg.tag);
     };
     imageObj.src = drgg.src;
}
function addObjects(x,tag,type,points){
	if(tag){
		if(tag!='Arrow'){
			$('#moves option').each(function(i,e){
				moves[$(this).val()].frame.push({category:tag,x:x.getPosition().x,y:x.getPosition().y,id:x.getId()});
			});
		}
		else if(!autoLine){
			var p=new Array();
			for(var i in points){
				p.push({x:points[i].x,y:points[i].y});
			}
			moves[$('#moves').val()].frame.push({category:tag,x:x.getAbsolutePosition().x,y:x.getAbsolutePosition().y,type:type,points:p,id:x.getId()});
		}
		autoLine=false;
	}
	layer.add(x);
	x.on('mouseover', function () {
		document.body.style.cursor = 'move';
	});
	x.on('mouseout', function () {
		document.body.style.cursor = 'default';
	});
	x.on('touchend', function () {
		document.body.style.cursor = 'default';
	});
	x.on('dragend', function () {
		var v;
		var sel=$('#moves').val();
		for(var i in moves[sel].frame){
			var v=moves[sel].frame[i];
			if(v.id==this.getId()){
				v.x=this.getAbsolutePosition().x;
				v.y=this.getAbsolutePosition().y;
				break;
			}
		}
	});
	x.on('click', function () {
		if(glob && glob.children && glob.children.length>1)
			glob.children[glob.children.length-1].setOpacity(0);
		glob='';
		layer.drawScene();
		if(dblclick){
			this.children[this.children.length-1].setOpacity(0.5);
			glob=this;
			dblclick=false;
			layer.drawScene();
			return;
		}
		dblclick=true;
		var clicking=setTimeout(function(){
			dblclick=false;
		},300);
	});
	stage.add(layer);
	layer.drawScene();
}
var bklayer;
function setBackground(src,ht){
	if(bklayer) bklayer.destroy();
	bklayer=new Kinetic.Layer();
	var imageObj = new Image();
	imageObj.onload = function() {
	  var bkimage=new Kinetic.Image({
			x:0,
			y:0,
			image:imageObj,
			width:306,
			height:ht
		});
		bklayer.add(bkimage);
		stage.add(bklayer);
		kin=true;
			stage.add(layer);
			initStage();
	};
	imageObj.src =src;
}

var stx, sty,moving,line,grp=new Array();;
function clearAllEvents(){
	stage.off("mousemove", drawing);
	stage.off("mouseup", endDrawing);
	stage.off("touchend", endDrawing);
	stage.off("click", s_drawing);
	stage.off("tap", s_endDrawing);
}
var clicked=false,started=false,pass=false,arrow=true,jogged=false;

function sline(def,arr,jogg){
	if(def) pass=true;
	if(arr) arrow=false;
	if(jogg) jogged=true;
	/*if(started){
		if(grp)
			grp.remove();
	}*/
	grp.push(new Kinetic.Group({
		draggable:true,
		id:Date.now()
	}));
	clearAllEvents();
	started=true;
	clicked=false;
	moving = false;
	stage.on("mouseup", endDrawing);
	stage.on("touchend", endDrawing);
	return false;
}
function drawing(e){
	if (moving) {
		var mousePos = stage.getMousePosition();
		var x = mousePos.x;
		var y = mousePos.y;
		line.getPoints()[1].x = mousePos.x;
		line.getPoints()[1].y = mousePos.y;
		moving = true;
		layer.drawScene();
	}
}
function endDrawing(e){
		var mousePos = stage.getMousePosition();
		if(touch) { mousePos = stage.getTouchPosition(); }
	if(!clicked){
		clicked=true;
		stage.on("mousemove", drawing);
		stx=mousePos.x,
		sty=mousePos.y;
		//CHANGED - Create new line
		grp.push(new Kinetic.Group({
			draggable:true,
			id:Date.now()
		}));
		line = new Kinetic.Line({
			points: [0, 0, 50, 50],
			stroke: "black",
			strokeWidth: 2,
			dashArray:[10,5],
			dashArrayEnabled:pass
		});
		//CHANGED - Add line to layer
		grp[grp.length-1].add(line);
		layer.add(grp[grp.length-1]);
		//start point and end point are the same
		line.getPoints()[0].x = mousePos.x;
		line.getPoints()[0].y = mousePos.y;
		line.getPoints()[1].x = mousePos.x;
		line.getPoints()[1].y = mousePos.y;
		moving = true;
		layer.drawScene();
	}
	else{
		//canvas_arrow(stx,sty,mousePos.x,mousePos.y);
		draw_Lines(mousePos.x,mousePos.y);
	}
}
function draw_Lines(x,y,ln){
	var type=1;
	if(!ln) ln=grp.length-1;
		if(pass) type=2;
		if(!arrow) type=4;
		moving = false;
		clicked=false;
		line.getPoints()[1].x = x;
		line.getPoints()[1].y = y;
		var hexagon;
		if(arrow){
			hexagon = new Kinetic.RegularPolygon({
				x: line.getPoints()[1].x,
				y: line.getPoints()[1].y,
				sides: 3,
				radius: 8,
				fill: 'black',
				stroke: 'black',
				strokeWidth: 1,
				rotation: 	-Math.atan2(line.getPoints()[0].x-line.getPoints()[1].x,line.getPoints()[0].y-line.getPoints()[1].y)
			});
		}
		else{
			hexagon=new Kinetic.Line({
				x:line.getPoints()[1].x,
				y:line.getPoints()[1].y,
				points:[{x:0,y:-10},{x:0,y:10}],
				stroke: "black",
				strokeWidth: 2,
				rotation:Math.PI/2-Math.atan2(line.getPoints()[0].x-line.getPoints()[1].x,line.getPoints()[0].y-line.getPoints()[1].y)
			});
		}
		var xx=line.getPoints()[1].x;
		var yy=line.getPoints()[1].y;
		if(line.getPoints()[1].x>line.getPoints()[0].x)
			xx=line.getPoints()[0].x;
		if(line.getPoints()[1].y>line.getPoints()[0].y)
			yy=line.getPoints()[0].y;
		var w=Math.abs(line.getPoints()[1].x-line.getPoints()[0].x);
		var h=Math.abs(line.getPoints()[1].y-line.getPoints()[0].y);
		var rect=new Kinetic.Rect({
			x: xx,
			y: yy,
			fill: 'black',
			stroke: 'black',
			strokeWidth: 1,
			width:w,
			height:h,
			opacity:0
		});
		if(jogged){
			 var imageObj=new Image();//new Array();
			  var group=new Kinetic.Group({
				  x:line.getPoints()[0].x,
				  y:line.getPoints()[0].y,
				  offsetY:6.5,
					rotation:-Math.PI/2-Math.atan2(line.getPoints()[0].x-line.getPoints()[1].x,line.getPoints()[0].y-line.getPoints()[1].y)
			  });
			var lngth=Math.sqrt(Math.pow(line.getPoints()[1].x-line.getPoints()[0].x,2)+Math.pow(line.getPoints()[1].y-line.getPoints()[0].y,2));
			imageObj.onload = function() {
			  for(var i=0;i<lngth-13;i+=13){
				 // imageObj.push(new Image());
					  var bkimage=new Kinetic.Image({
							x:i,
							y:0,
							image:imageObj,
							width:13,
							height:13
						});
						group.add(bkimage);

			  }
			   var bkimage=new Kinetic.Image({
							x:i,
							y:0,
							image:imageObj,
							width:lngth-i,
							height:13
						});
					grp[ln].destroyChildren();
					group.add(bkimage);
					grp[ln].add(hexagon);
					grp[ln].add(group);
					grp[ln].add(rect);
					grp[ln].remove();
					addObjects(grp[ln],'Arrow',3,line.getPoints());
					clearAllEvents();
				};
				imageObj.src ='Model/Img/dribble.png';
		}
		else{
			grp[ln].add(hexagon);
			grp[ln].add(rect);
			grp[ln].remove();
			addObjects(grp[ln],'Arrow',type,line.getPoints());
			clearAllEvents();
		}
		started=false;
		pass=false;arrow=true;jogged=false;
}
function s_pline(){
	grp.push(new Kinetic.Group({
		draggable:true,
		id:Date.now()
	}));
	started=true;
	clicked=false;
	clearAllEvents();
	//stage.on("dblclick", s_endDrawing);
	clicking=setTimeout(function(){

	},300);
	stage.on("click", s_drawing);
	stage.on("tap", s_drawing);
	layer.add(grp[grp.length-1]);
	return false;
}
var s_points=new Array(),dblclick=false;
function s_drawing(e){
	if(dblclick){
		s_points.pop();
		s_endDrawing();
		dblclick=false;
		return;
	}
		var mousePos = stage.getMousePosition();
		if(touch) { mousePos = stage.getTouchPosition(); }
		var x = mousePos.x;
		var y = mousePos.y;
		var cir=new Kinetic.Circle({
			x: x,
			y: y,
			radius: 2,
			fill: 'black',
			stroke: 'black',
			strokeWidth: 1
		});
		s_points.push({x:x,y:y});
		grp[grp.length-1].add(cir);
		layer.draw();
		dblclick=true;
		var clicking=setTimeout(function(){
			dblclick=false;
		},300);
}
function s_endDrawing(e){
		var mousePos = stage.getMousePosition();
		if(touch) { mousePos = stage.getTouchPosition(); }
		var x = mousePos.x;
		var y = mousePos.y;
		s_points.push({x:x,y:y});
		drawSpline();

}
function drawSpline(ln){
	if(!ln) ln=grp.length-1;
	var ll = new Kinetic.Spline({
			points: s_points,
			stroke: 'black',
			strokeWidth: 2,
			lineCap: 'round',
			tension: .5
      	});
		var	hexagon = new Kinetic.RegularPolygon({
				x: s_points[s_points.length-1].x,
				y: s_points[s_points.length-1].y,
				sides: 3,
				radius: 5,
				fill: 'black',
				stroke: 'black',
				strokeWidth: 1,
				rotation: 	Math.PI/2-Math.atan2(s_points[s_points.length-1].x-s_points[s_points.length-2].x,s_points[s_points.length-1].y-s_points[s_points.length-2].y)
			});

		grp[ln].destroyChildren();
		/*grp=new Kinetic.Group({
			draggable:true
		});*/
		var xx=1000,yy=1000,w=0,h=0;
		for(var i in s_points){
			if(s_points[i].x<xx)
				xx=s_points[i].x;
			if(s_points[i].y<yy)
				yy=s_points[i].y;
			if(s_points[i].x>w)
				w=s_points[i].x;
			if(s_points[i].y>h)
				h=s_points[i].y;
		}
		var rect=new Kinetic.Rect({
			x: xx,
			y: yy,
			fill: 'black',
			stroke: 'black',
			strokeWidth: 1,
			width:w-xx,
			height:h-yy,
			opacity:0
		});
		grp[ln].add(hexagon);
		grp[ln].add(ll);
		grp[ln].add(rect);
		grp[ln].remove();
		addObjects(grp[ln],'Arrow',5,s_points);
		s_points=new Array();
	clearAllEvents();
}
