function loadGame()
{
	//// console.log('loadGame! m_t.js');

    var c=location.href.split('\x23')[0].split('\x3f')[1];

    //// console.log('c? ', c);

    if(c){

        var g=user.id;
        (c.split('\x26').length>1)?
            (g=c.split('\x26')[1].split('\x3d')[1],c=c.split('\x26')[0].split('\x3d')[1]):
            c=c.split('\x3d')[1];
//        if(g==''){
//            alert('Kindly login again to proceed.');
//            loadElements();
//            filenm='Name';
//            fileid='';
//            $('#userid').val(user.id);
//            $('#id').val('');
//            return false;
//        }
        showLoading();
        $.ajax({url:'load.php',data:{id:c,user_id:g},dataType:'json',success:function(n)
        {
        	//// console.log('n.play: ', n.play);
        	//// console.log('n.play.frames: ', n.play.frames);

            if(!n.play)
            {
            	alert('There is an error parsing the saved data, Administrator will be notified. \nSorry for inconvenience.');$.ajax({url:'email.php',data:{sender:'user',receiver:'',msg:'ERRROR',purl:location.href},success:function(d){location.href='play.php'}});
            	return false
            }

            halfcourt=(n.play.halfcourt=="true"?true:false);hash=n.play.skin;
            moves=n.play.frames;filenm=n.play.name;fileid=n.id;$('#userid').val(g);user.uped=n.uped;user.downed=n.downed;
            $('#id').val(n.id);

            $('#tags').val(n.tags);
            $('#tags-xs').val(n.tags);

            $('#speed').val(n.play.speed);
            $('#moves').html('');
            for(var i in moves){
                $('#moves').append($('<option>',{value:i,text:moves[i].name}));
            }
            var o=($('skin#'+hash,d));
            showLoading(true);loadElements(true);$('#court').val(halfcourt?1:0)

        },error:function(d)
        {
            // console.log('ajax error: ', d.responseText);
            alert('Your internet connection is lost or the server encountered an error.');
            loadElements();filenm='Name';fileid='';$('#userid').val(user.id);$('#id').val('')
        }
    })
    }else{
		loadElements();filenm='Name';fileid='';
		$('#userid').val(user.id);
		$('#id').val('')
    }
}

function setUser(f){
	////// console.log('SetUser main.js!');
		(f)?
		(
			// $('#logged>div').show(),
			$('#logreglinks-container').hide(),
			$('#logreglinks').hide(),

			$('#logged>div').hide(),
			$('#logged>div>img').attr('src',user.avatarurl),

			$('#name').html(user.lastname),$('#logout_btn').show(),
			$('#log').hide(),

			$('.myplay').show()

			//$('#playerList').show(),
			//$('#loginLog').show()

		):
		(
			$('#logreglinks').show(),

			$('#logged>div').hide(),

			$('#logout_btn').hide(),
			$('.myplay').hide(),
			$('#log').show(),
			$('#lists>table').html('')
			// remove any rows in playList
			//$('#playerList').hide(),
			//$('#loginLog').hide()

		)
	}

	function getParamFromUrl(param_name){
		params = location.search.replace('?', '').split('&');
		for (var i = 0; i < params.length; i++) {
			var key_value = params[i].split('=');
			if(key_value[0] == param_name){
				return key_value[1];
			}
		};
	}

	function loadScreens(c,g,n)
	{
		// c = id=#, g=array?, n=
		////// console.log('loadScreens: ', c);
		////// console.log('loadScreens: ', g);
		////// console.log('loadScreens: ', n);

		$('#moves').val(c);loadLayer();
		setTimeout(function(){
			stage.toDataURL({
				callback:function(d){
					img+='\x2c'+d.split('\x2c')[1];c++;$('#loadtxt').html('Processing '+$('#moves option:selected').text()+'...');
					(moves.length==c)?(pdff=true,showLoading(true),img=img.substr(1),saving(g,n),pdff=false,$('#loadtxt').html('Processing...')):loadScreens(c,g,n)
				}
			})
		},1000)
	}

	function saveG(c,g)
	{
		////// console.log('saveg clicked main c,g: ', c, g);

		if(getParamFromUrl('user') != undefined && getParamFromUrl('user') != user.id)
		{
			alert('This play belongs to somebody else. You cannot modify it.');
			return false;
		}

		(c)?(showLoading(),img='',loadScreens(0,c,g)):0
	}

	function saving(c,g)
	{
		console.log("saving main c: "+ c + " g: " + g);
		var n='',o='';
		for(var i=0;i<moves.length;++i)
		{
			// n = movements string, o = comments string
			n+='\x60'+moves[i].name;
			o+='\x60'+moves[i].comment;
		}

		// only show loading if this isn't a new play
		if (g)
		{
			showLoading(false);
		}

		var tag = $.trim($('#save_tag').val().toLowerCase());
		if(tag.length == 0)
		{
			tag = $.trim($('#tags').val().toLowerCase());

            if(tag.length == 0)
            {
                tag = $.trim($('#tags-xs').val().toLowerCase());
            }
		}

        var scout = $('#scout').val().trim();
    	if(scout.length == 0)
    	{
    		scout = $('#scout-xs').val().trim();
    	}

		(g)?
		($.ajax({type:'POST',url:'save.php',
			data:{
				id:fileid,
				names:n,
				comment:o,
				private:$('#private:checked').length,
				img:img,
				tags:tag,
				scout:scout,
				data:{halfcourt:halfcourt,name:filenm,names:n,comment:o,skin:hash,frames:moves,speed:$('#speed').val()}
			},
			dataType:'xml',
			success:function(d){
				(
                    $('status',d).text()=='\x31') ? (
					$('#savegame-modal').modal('hide'),
                    showLoading(true),
                    $('#savename').val(''),
                    $('#id').val($('id',d).text())) : 0;

                    alert($('msg',d).text());
                    // showLoading(true);
			}
		})):
		(
			$.ajax({
				type:'POST',
				url:'save.php',
				data:{
					uid:user.id,cat:$('#cat').val(),
					name:c,
					names:n,
					private:$('#private:checked').length,
					img:img,
					dataType:"xml",
					comment:o,
					tags:tag,
					scout:scout,
					data:{halfcourt:halfcourt,name:c,skin:hash,frames:moves,speed:$('#speed').val()}
				},
				success:function(d)
				{
					// console.log('save success: ', $('status',d).text());
					($('status',d).text()=='\x31')?(
						// $('#saveGame').hide('slow')
						$('#savegame-modal').modal('hide')

						,$('#lrg').hide('slow'),$('#savename').val(''),$('#id').val($('id',d).text())):0;
					alert($('msg',d).text());
					if($('status',d).text() != '0')
					{
//						////// console.log('reload');
						location.href="play.php?id="+$('id',d).text()+"&user="+user.id+"&name="+c+"&category="+$('#cat option:selected').text();
					}
					showLoading(true)
				}
			})
		)
	}

	// var dragging
	function addObjects(x,c,g,n)
	{

		// // console.log('addObjects main! x: ' + JSON.stringify(x) + ' c: ' + c + ' g: ' + g + ' n: ' + n);
		if(c){
			if(c !== 'Arrow')
			{
				// //// console.log('c != Arrow!');

				var floatX = x.getPosition().x;
				var floatY = x.getPosition().y;

				//// console.log('floatX? ' + floatX + ' floatY? ' + floatY);

				moves[$('#moves option:selected').val()].frame.push({category:c, x:floatX, y:floatY, id:x.getId()});


			} else if(!autoLine) {

				// //// console.log('Drag NOT autoLine!');

				var p=new Array();
				for(var i in n){p.push({x:n[i].x,y:n[i].y})}
				moves[$('#moves').val()].frame.push({
					category:c,
					x:x.getAbsolutePosition().x,
					y:x.getAbsolutePosition().y,
					type:g,
					points:p,
					id:x.getId()
				})
			}

			autoLine=false
		}

		layer.add(x);

		x.on('mouseover',function(){document.body.style.cursor='move'});
		x.on('mouseout',function(){document.body.style.cursor='default'});
		x.on('touchend',function()
		{
			////// console.log('touchend main');

			document.body.style.cursor='default'
		});
		x.on('dragstart', function()
		{
			//// console.log('dragstart x? ' + this.getAbsolutePosition().x + ' y? ' + this.getAbsolutePosition().y);
			toggleCourtPlay(false);
			////// console.log('dragstart usingTool? ', usingTool);
		});
		x.on('dragend',function()
		{
			//// console.log('dragend x?' + this.getAbsolutePosition().x + ' y? ' + this.getAbsolutePosition().y);

			var v,o=$('#moves').val();
			////// console.log('moves length: ', moves.length);
			for(var i in moves[o].frame)
			{
				var v=moves[o].frame[i];
				if(v.id==this.getId())
				{
					v.x=this.getAbsolutePosition().x;
					v.y=this.getAbsolutePosition().y;
					break
				}
			}

			// reset tool user to allow container clicks
			setTimeout(function() { toggleCourtPlay(true); }, 300);

			////// console.log('dragend main usingTool? ', usingTool);
		});

		x.on('click',function()
		{
			toggleCourtPlay(false);
			// //// console.log('click main usingTool? ', usingTool);

			(glob&&glob.children&&glob.children.length>1)?glob.children[glob.children.length-1].setOpacity(0):0;glob='';layer.drawScene();
			if(dblclick)
			{
				this.children[this.children.length-1].setOpacity(0.5)
				glob=this;dblclick=false;layer.drawScene();

				setTimeout(function() { toggleCourtPlay(true); }, 300);

				return
			}dblclick=true;
			var o=setTimeout(function()
			{
				dblclick=false;

				setTimeout(function() { toggleCourtPlay(true); }, 300);

			},300);




		});
		stage.add(layer);
		layer.drawScene()
	}

	function addMovement(movement_name, comment)
	{
		// ////// console.log('Add Movement! ', movement_name, comment);
		var c=$('#moves').val(),g=new Array();
		for(var i=0;i<moves[moves.length-1].frame.length;++i)
		{
			(moves[moves.length-1].frame[i].category!='Arrow')?g.push($.extend(true,{},moves[moves.length-1].frame[i])):0
		}

		// var movement_name = prompt('Enter movement name:');
		if(movement_name.trim().length == 0)
			movement_name = 'Movement '+(moves.length+1);

		var m={name:movement_name, frame:g,comment:comment};
		moves.push(m);
		$('#moves').append($('<option>',{value:moves.length-1,text:movement_name}));
		$('#moves').val(moves.length-1);
		$('#moves').trigger('change');
	}

	function loadElements(l)
	{
		////// console.log('Load elements!');
		var first_movement_name = 'Initial Set';
		showLoading();

		$.ajax({
			url:'system_playbook_test.xml',
			dataType:'xml',
			success:function(c){
				d=c;stage=new Kinetic.Stage({container:'container',width:306,height:509});
				layer=new Kinetic.Layer();
				changeSkin();
				if(!l){
					var m={name:first_movement_name,comment:'',frame:new Array()};
					moves.push(m);
					$('#moves').html($('<option>',{value:0,text:first_movement_name,selected:'selected'}))
				}
				$('#moves').val(0);
				$('#moves').trigger('change');
				showLoading(true)
			}
		})

	}

	$(function(){
		$('#login_btn').click(function(e)
        {
			if($('#email').val().trim().length==0 || $('#pass').val().trim().length==0){
				alert('Username or password is empty!');
				return false;
			}
			////// console.log('logging in this: ', $(this));

			// show loading glyphicon animation
           	$(this).find('.glyphicon-refresh').addClass('glyphicon-refresh-animate');

			$.ajax({
				url:'login.php',
				data:{mail:$('#email').val(),pwd:$('#pass').val()},
				type:'POST',
				dataType:"xml",
				success:function(d)
				{
                    // console.log('Login success!', d);
					xml=d;($('status',d).text()=='\x31')?(location.reload()):(alert("Your email or password doesn't match"),user={id:'',lastname:'',email:'',ismale:'',role:'',profileurl:'',avatarurl:'',hoopid:''},setUser(false));
					// showLoading(true)

					// hide loading glyphicon animation
           			$(this).find('.glyphicon-refresh').removeClass('glyphicon-refresh-animate');
           			$('#login-modal').modal('hide');
				}
			})
		});

		$('#logout_btn').click(function(e){
			showLoading();
			$.ajax({url:'logout.php',
				data:{},
				success:function(d){
					user={id:'',lastname:'',email:'',ismale:'',role:'',profileurl:'',avatarurl:'',hoopid:''};
					setUser(false);
					$('#id').val('');
					showLoading(true);
					location.href = '/basketball-plays.php';
				}
			})
		});

		$('#save_btn').click(function(e)
		{
			////// console.log('save_btn clicked e: ', e);
			var c=location.href.split('\x23')[0].split('\x3f')[1];
			////// console.log('save_btn clicked c: ', c);

			if(typeof user == 'undefined' || user.id=='')
			{
				alert("You'll need to register to save this game.");return false
			}
			if(!c&&user.id!='')
			{
				// $('#saveGame').show('slow');$('#lrg').show('slow')
				$('#savegame-modal').modal('show');

 			} else {

				var g='';
				(c.split('\x26').length>1) ? (g=c.split('\x26')[1].split('\x3d')[1],c=c.split('\x26')[0].split('\x3d')[1]) : c=c.split('\x3d')[1];
				////console.log(g);
				// (g!='' && g==user.id) ? saveG(c,true) : alert("You don't have the permission to edit this Movements.")
				saveG(c,true);
			}
		});

		$('#pdf_form').submit(function(e)
		{
            //// console.log('Download pdf!!');

			var c=$('#moves').val(),g=location.href.split('\x23')[0].split('\x3f')[1];

			if(!g&&($('#id').val()==''||$('#id').val()=='\x30'))
            {
				alert('Save the Frames first.');return false

			} else if (g.split('\x26').length>1) {

				$('#user_id').val(g.split('\x26')[1].split('\x3d')[1]);$('#id').val(g.split('\x26')[0].split('\x3d')[1]);

			} else {

				$('#id').val(g.split('\x3d')[1]);

				if(user.id!='')
                {
					$('#user_id').val(user.id);

				} else {

					alert('Your session has expired please login again.');
					return false
				}
			}
			return true
		});

		$('#add_movement').click(function()
		{
			$('#new_movement_name').val('');
			$('#new_movement_comments').val('');
			// $('#new_movement_dialog').dialog('open');

		});

		$('#savmove_btn').click(function()
		{
			////// console.log('new move name: ', $('#new_movement_name').val());
			////// console.log('new move comments: ', $('#new_movement_comments').val());

			addMovement($('#new_movement_name').val(), $('#new_movement_comments').val());
			$('#comment').val($('#new_movement_comments').val());
			$('#newmove-modal').modal('hide')
		});

		$('#new_movement_dialog').dialog({
			width:400,
			modal: true,
			autoOpen: false,
			buttons:{
				'OK':function(){
					addMovement($('#new_movement_name').val(), $('#new_movement_comments').val());
					$('#comment').val($('#new_movement_comments').val());
					$('#new_movement_dialog').dialog('close');
				},
				'Cancel':function(){
					$('#new_movement_dialog').dialog('close');
				}
			}
		});

		$('#pdf_warning_dialog').dialog({
			modal: true,
			autoOpen: false,
			width: 350
		})
		$('#new_play_warning_dialog').dialog({
			modal: true,
			autoOpen: false,
			width: 350
		})

        loadGame();

        $('#menu').superfish({
            delay:200
        });
	})
