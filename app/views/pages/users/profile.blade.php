	@extends('layouts.profilemaster')
@section('content')    

<script type="text/javascript">
    function MM_jumpMenu(targ,selObj,restore){ //v3.0
        eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
        if (restore) selObj.selectedIndex=0;
    }
</script>
<style>
	.btn-join-wrap {
		display:none!important;
	}
	
    .navbar{
        margin-bottom: 0;
    }
    .addtour-wrap {
        float:left;
        width:100%;
        margin:0;
        padding:0;
    }

    .addtour-wrap li {
        float:left;
        list-style:none;
        width:100%;
        padding:0;
        margin-bottom:5px;
    }
    .datepicker
    {
        z-index: 1052!important;
    }
    .shown{
        display: block;
    }
    .hidden{
        display: none;
    }
    /*.loading {
        z-index:    1000;
        height:     100%;
        width:      100%;
        background: rgba( 255, 255, 255, .8 ) 
                    url('http://sampsonresume.com/labs/pIkfp.gif') 
                    50% 50% 
                    no-repeat;
    }*/
    .pager .page-number  {
            position: relative;
            float: left;
            padding: 6px 12px;
            margin-left: -1px;
            line-height: 1.42857143;
            color: #428bca;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }

            .pager .page-number:first-child {
                border-radius: 5px 0 0 5px;
                border-left: solid 1px #ccc;
            }

            .pager .page-number:last-child {
                border-radius: 0 5px 5px 0;
            }

            .pager .page-number:hover, .pager .page-number:focus, .pager .active:hover, .pager .active:focus{
                color: #2a6496;
                background-color: #eee;
                border-color: #ddd;
            }
    .pager .active:hover, .pager .active:focus {
        cursor: not-allowed;
    }

    .pager .active {
        cursor: default;
        background-color: #eee!important;
    }
</style>
<script type="text/javascript" src="/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    var jq = $.noConflict();
    jq(document).ready(function()
    {
        jq('#datepicker-tournament').datepicker({
            format: 'yyyy-mm-dd'
        });
    });
</script>
<script src="/js/jquery.min.js"></script>
<script type="text/javascript">

    $(document).ready(function()
    {
        $('#datepicker-tournament').val(moment().format("YYYY-MM-DD"));

        $('#search-tournament-button').click(function(e)
        {
            e.preventDefault();
            $('.loading-tournament').show();
            $('.search-tournament-wrapper').hide();
            $('.tournaments-table-wrapper').hide();
            
            var network = $('#select-network').val();
            var date = $('#datepicker-tournament').val();
            var keyword = $('#search-tournament-keyword').val();
            //alert(network + " - " + date + " - " + keyword);
            $.ajax({
                type: "POST",
                url: "/packages/ajax/gettournaments",
                data: { network: network, date: date, keyword: keyword },
                cache: false,
                success: function(data){
                    //alert("Tournament found : " + data.count);

                    // $('#send-comment').data('packageid', packageid);
                    // $('#hiddenpackageid').val(packageid);
                    if(data.success == "success")
                    {
                        var html = '';
                        html += '<div class="tournament-found"> Tournament found : '+ data.count +' tournament(s) </div>'
                        //console.log(data.comments);
                        html += '<table class="table table-striped paginated">';
                        
                        html += '<thead><tr>';
                        html += '<th>No</th>';
                        html += '<th>Tournament Name</th>';
                        html += '<th>Start Date</th>';
                        html += '<th>Stake</th>';
                        html += '<th>Rake</th>';
                        html += '<th>Action</th>';
                        html += '</tr></thead>';
                        html += '<tbody>';
                        for (var i = 0; i < data.tournaments.length; i++) {

                            var tournament = data.tournaments[i];

                            //console.log(comment);
                            html += '<tr id="tournament-'+ tournament.id +'">';
                            html += '<td>'+ (i+1) +'</td>';
                            html += '<td> <a target="_blank" href="http://www.sharkscope.com/#Find-Tournament//networks/'+ tournament.network +'/tournaments/'+ tournament.game_id +'">'+ tournament.game_name +'</a></td>';
                            html += '<td>'+ tournament.start_date +'</td>';
                            html += '<td>'+ tournament.stake +'</td>';
                            html += '<td>'+ tournament.rake +'</td>';
                            html += '<td><a href="#" data-buyin="' + ((tournament.stake*1) + (tournament.rake*1)) +'" data-gamename="'+ tournament.game_name +'" data-network="'+ tournament.network +'" data-gameid="' + tournament.game_id + '" class="add-tournament"><span class="glyphicon glyphicon-plus"></span>  Add Tournament</a></td>';
                            html += '</tr>';
                        }
                        html += '</tbody>';
                        html += '</table>';
                        $('.tournaments-table-wrapper').html(html);

                        $('table.paginated').each(function() {
                            var currentPage = 0;
                            var numPerPage = 10;
                            var ten = 0;
                            var $table = $(this);
                            $table.bind('repaginate', function() {
                                $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                            });
                            $table.trigger('repaginate');
                            var numRows = $table.find('tbody tr').length;
                            var numPages = Math.ceil(numRows / numPerPage);
                            var maxTen = Math.floor(numRows / 100) * 10;
                            
                            var $pager = $('<div class="pager"></div>');
                            //First
                            $('<span class="page-number"></span>').text('First').bind('click', {
                                newPage: 0
                            }, function(event) {
                                currentPage = event.data['newPage'];
                                ten = 0;
                                if(numPages > 10)
                                {
                                    var first = $('span#page-' + 1);
                                    $('span.shown').addClass('hidden').removeClass('shown');
                                    for (var i = 0; i < 10; i++) {
                                        first.removeClass('hidden').addClass('shown');
                                        if(first.next('span.hidden').length != 0)
                                        {
                                            first = first.next();
                                            
                                        }
                                    };
                                    $('.prev-ten').hide();
                                    $('.next-ten').show();
                                }
                                $table.trigger('repaginate');
                                $(this).next().next().addClass('active').siblings().removeClass('active');
                            }).appendTo($pager).addClass('clickable');

                            //Prev Ten
                            $('<span class="page-number prev-ten" style="display:none"></span>').text('...').bind('click', {
                                newPage: (ten - 10)
                            }, function(event) {
                                currentPage = (ten - 1);//event.data['newPage'];
                                ten = (ten - 10);
                                
                                var firstShown = $pager.find('span.shown:first');
                                var lastShown = $pager.find('span.shown:last');
                                firstShown.prev().addClass('active').siblings().removeClass('active');

                                if(ten == 0)
                                {
                                    $('.prev-ten').hide();
                                }

                                var activePage = firstShown.prev().data('page');
                                for (var i = 0; i < 10; i++) {
                                    lastShown.removeClass('shown').addClass('hidden');
                                    if(firstShown.prev('span.hidden').length != 0)
                                    {
                                        firstShown.prev().removeClass('hidden').addClass('shown');
                                        firstShown = firstShown.prev();
                                    }
                                    //console.log($(lastShown).hasClass('active'));
                                    if(lastShown.prev().data('page') > activePage)
                                    {
                                        lastShown = lastShown.prev();
                                    }
                                }

                                $('.next-ten').show();
                                $table.trigger('repaginate');
                                //$(this).prev().addClass('active').siblings().removeClass('active');
                            }).appendTo($pager).addClass('clickable');

                            //Number
                            for (var page = 0; page < numPages; page++) {
                                var row = $('<span id="page-'+ (page+1) +'" data-page="'+ (page+1) +'" class="page-number shown"></span>').text(page + 1).bind('click', {
                                    newPage: page
                                }, function(event) {
                                    var lastPage = currentPage;
                                    currentPage = event.data['newPage'];

                                    $table.trigger('repaginate');
                                    $(this).addClass('active').siblings().removeClass('active');
                                }).appendTo($pager).addClass('clickable');

                                if(numPages > numPerPage)
                                {
                                    if(page + 1 > (currentPage + 10) || page - 1 < (currentPage - 10))
                                    {
                                        row.removeClass('shown').addClass('hidden');
                                    }
                                }
                            }

                            //Next Ten
                            $('<span class="page-number next-ten"></span>').text('...').bind('click', {
                                newPage: (ten + 10)
                            }, function(event) {
                                currentPage = (ten + 10);//event.data['newPage'];
                                ten = (ten + 10);
                                
                                var firstShown = $pager.find('span.shown:first');
                                var lastShown = $pager.find('span.shown:last');
                                console.log(ten + ' ; ' + maxTen );
                                if(ten >= maxTen)
                                {
                                    $('.next-ten').hide();
                                }

                                lastShown.next().addClass('active').siblings().removeClass('active');
                                for (var i = 0; i < 10; i++) {
                                    firstShown.removeClass('shown').addClass('hidden');
                                    if(lastShown.next('span.hidden').length != 0)
                                    {
                                        lastShown.next().removeClass('hidden').addClass('shown');
                                        lastShown = lastShown.next();
                                        
                                    }
                                    firstShown = firstShown.next();
                                };
                                $('.prev-ten').show();
                                $table.trigger('repaginate');
                                //$(this).prev().addClass('active').siblings().removeClass('active');
                            }).appendTo($pager).addClass('clickable');

                            //Last
                            $('<span class="page-number"></span>').text('Last').bind('click', {
                                newPage: numPages-1
                            }, function(event) {
                                currentPage = event.data['newPage'];
                                ten = maxTen;
                                if(numPages > 10)
                                {
                                    var last = $('span#page-' + (maxTen+1));
                                    $('span.shown').addClass('hidden').removeClass('shown');
                                    for (var i = 0; i < 10; i++) {
                                        last.removeClass('hidden').addClass('shown');
                                        if(last.next('span.hidden').length != 0)
                                        {
                                            last = last.next();
                                            
                                        }
                                    };
                                    $('.prev-ten').show();
                                    $('.next-ten').hide();
                                }
                                
                                $table.trigger('repaginate');
                                $(this).prev().prev().addClass('active').siblings().removeClass('active');
                            }).appendTo($pager).addClass('clickable');
                            
                            if(numRows > 10)
                            {
                                $pager.insertAfter($table).find('span.page-number:first').next().next().addClass('active');
                            }
                            
                            if(numPages < 10)
                            {
                                $('.next-ten').hide();
                            }
                        });

                        $('.add-tournament').each(function(){
                            $(this).click(function(e){
                                e.preventDefault();
                                alert($(this).data('gamename') + ' selected!');
                                var tournamentRow = "<li>";
                                tournamentRow += '<div class="row addtour">';
                                tournamentRow += '<div class="form-group col-md-5">';
                                tournamentRow += $(this).data('gamename');
                                tournamentRow += '<input id="turnament[]" name="turnament[]" value="' + $(this).data('gamename') + '" type="hidden">';
                                tournamentRow += '<input id="gameid[]" name="gameid[]" value="' + $(this).data('gameid') + '" type="hidden">';
                                tournamentRow += '</div>';
                                tournamentRow += '<div class="form-group col-md-2">';
                                tournamentRow += $(this).data('network');
                                tournamentRow += '</div>';
                                tournamentRow += '<div class="form-group col-md-3">';
                                tournamentRow += '<input class="form-control hitung" id="buyin[]" value="'+ $(this).data('buyin') +'" name="buyin[]" placeholder="Enter Buy In Amount" type="text" required>';
                                tournamentRow += '</div>';
                                tournamentRow += '<div class="form-group col-md-2">';
                                tournamentRow += '<button class="btn btn-danger remove-tournament"> Remove </button> ';
                                tournamentRow += '</div>';
                                tournamentRow += '</div>';
                                tournamentRow += '</li>';
                                $('.addtour-wrap').append(tournamentRow);
                                
                                $('.hitung').keyup(function () { 
                                    callBuyin();
                                });

                                $('.remove-tournament').each(function(){
                                    $(this).click(function(e){
                                        e.preventDefault();
                                        $(this).parent().parent().parent().remove();
                                        
                                        callBuyin();
                                    });
                                });

                                callBuyin();
                            });
                        });

                    }
                    else
                    {
                        //alert("empty");
                        var html = '';
                        html += '<div class="pull-left comment-content">';
                        html += 'No tournament found';
                        html += '</div>';
                        $('.tournaments-table-wrapper').html(html);
                        //console.log($('.comment-list').html());
                    }
                    //console.log( 'get comments ' + packageid + ' - '+ data.comments.length);

                    $('.loading-tournament').hide();
                    $('.search-tournament-wrapper').show(); 
                    $('.tournaments-table-wrapper').show();
                }
            });
        });
        
        function callBuyin(){
            var buyins = document.getElementsByName('buyin[]');
            var totalbuyin = 0;
            
            for (var i=0; i<buyins.length; i++)
            {
                totalbuyin += Number(buyins[i].value);
            }
            var selling = Number($('#add-selling').val());
            var sellingP = (selling/100);
            var total =  0;//(totalbuyin * sellingP) ;
            
            //alert(sellingP);
            
            var markup = 0;
            if($('#markup').val()=='') markup = 0;
            else markup = $('#markup').val();
            
            totalmarkup = totalbuyin * markup;
            
            selling_total = totalmarkup * sellingP;
            
            total = totalbuyin * markup;//(totalbuyin+selling_total)-(totalbuyin*sellingP);
            //jQuery('#keterangan').html('selling='+selling+' , selling % ='+ sellingP + ' , markup = '+ totalmarkup +' , totalbuyin = '+totalbuyin + ', total = '+ total);
                        
            $('#selling_amount').val(selling_total); 
            $('#selling_amount_form').val(selling_total);
            
            $('#total').val(total); 
            $('#total_form').val(total);
            
        }
         
        $('#selling').keyup(function () { 
            callBuyin();
        });
         
        $('#markup').keyup(function () { 
            callBuyin();
        });
         
        $('.hitung').keyup(function () { 
            callBuyin();
        });


        $('#submit-package').click(function(e){
            
            var isValid = true;
            var countTournament = $('.addtour-wrap li').length
            if(countTournament == 1)
            {
                isValid = false;
                alert('Please choose tournament');
            }
            var countChecked = 0;
            $('.checkbox-payment').each(function(){
                if(this.checked)
                {
                    countChecked++;
                }
            });
            if(countChecked == 0)
            {
                isValid = false;
                alert('Please choose payment');
            }
            if(!isValid)
            {
                e.preventDefault();
            }
            
        });

        $('#addcircle').click(function(e)
        {
            e.preventDefault();
            var userID = $(this).data("userid");

            $.post("/sendcirclerequest/" + userID, function( data ) {
                //alert(data);
                $('#addcircle').remove();
                var html = '<a href="#" class="btn btn-primary"><span class="checks">Add</span><b>Request Sent</b></a>'
                $('.user-btn-wrap').prepend(html);
                //alert( "Load was performed." );
            }, "json")
            .fail(function() {
                alert( "circle request failed" );
            });
        });
		
		$('#block-button').click(function(e)
        {
            e.preventDefault();
            var userID = $(this).data("userid");
			var userBlockID = $(this).data("userblockid");					
			
            $.post("/adduserblock/" + userBlockID, function( data ) {
                
                $('#block-button').remove();
                var html = '<a href="#" id="unblock-button" class="btn btn-primary" data-userid="'+userID+'"  data-userblockid="'+userBlockID+'"><span class="unblockuser">Unblock</span><b>Unblock this user</b></a>	'
                $('.user-btn-wrap').append(html);
                //alert( "Load was performed." );
            }, "json")
            .fail(function() {
                alert( "block user failed" );
            });	
        });
		
		$('#unblock-button').click(function(e)
        {
            e.preventDefault();
            var userID = $(this).data("userid");
			var userBlockID = $(this).data("userblockid");			
			
            $.post("/canceluserblock/" + userID +"/"+ userBlockID, function( data ) {
                
                $('#unblock-button').remove();
                var html = '<a href="#" id="block-button" class="btn btn-primary" data-userid="'+userID+'"  data-userblockid="'+userBlockID+'"><span class="blockuser">block</span><b>Block this user</b></a>	'
                $('.user-btn-wrap').append(html);
                //alert( "Load was performed." );
            }, "json")
            .fail(function() {
                alert( "block user failed" );
            });
        });
        
        $('#accept-button').click(function(e){
          e.preventDefault();
          var friendRequestID = $(this).data('friendrequestid');
          $.ajax({
            type: "POST",
            url: "/circle/ajax/accept",
            data: { query: friendRequestID },
            cache: false,
            success: function(html){
              $('#accept-button').remove();
              $('#reject-button').remove();
              console.log( 'accepted ' + friendRequestID);
            }
          });
          
        });

        
        $( '#reject-button' ).click(function(e){
          e.preventDefault();
          var friendRequestID = $(this).data('friendrequestid');
          //console.log(friendRequestID)
          $.ajax({
            type: "DELETE",
            url: "/circle/ajax/reject",
            data: { query: friendRequestID },
            cache: false,
            success: function(html){
              $('#accept-button').remove();
              $('#reject-button').remove();
              console.log( 'rejected ' + friendRequestID);
            }
          });
          
        });

        $('.btn-comment').each(function()
        {
            $(this).click(function (e)
            {
                e.preventDefault();
                $('.loading').show();
                $('.comment-list').hide();
                $('#modal-comment').modal('show');
                var packageid = $(this).data('packageid');
                $.ajax({
                    type: "POST",
                    url: "/packages/ajax/getcomment",
                    data: { packageid: packageid },
                    cache: false,
                    success: function(data){
                        $('#send-comment').data('packageid', packageid);
                        $('#hiddenpackageid').val(packageid);
                        if(data.success == "success")
                        {
                            var html = '';
                            //console.log(data.comments);
                            for (var i = 0; i < data.comments.length; i++) {
                                var comment = data.comments[i];
                                //console.log(comment);
                                html += '<div class="pull-left comment-content">';
                                html += '<div class="image-wrap">';
                                html += '<img src="'+ comment.image +'" alt="">'
                                html += '</div>';
                                html += '<div class="pull-left content-text-wrap">';
                                html += '<p><b>'+ comment.fullname +'</b> ' +  comment.comment + '</p>';
                                html += '<p class="font-sm datetime-format" data-messagetime="' + comment.created.date + '">'+ comment.created.date +'</p>';
                                html += '</div>';
                                html += '</div>';
                            }

                            $('.comment-list').html(html);

                            $('.datetime-format').each(function() {
                                var dateTime = $(this).data('messagetime');
                                //$(this).text(DateFormat.format.prettyDate(dateTime));
                                $(this).text(moment(dateTime, 'YYYY-MM-DD HH:mm Z').fromNow());

                            });

                        }
                        else
                        {
                            //alert("empty");
                            var html = '';
                            html += '<div class="pull-left comment-content">';
                            html += 'No Comment yet';
                            html += '</div>';
                            $('.comment-list').html(html);
                            //console.log($('.comment-list').html());
                        }
                        //console.log( 'get comments ' + packageid + ' - '+ data.comments.length);

                        $('.loading').hide();
                        $('.comment-list').show(); 
                    }
                });
                
                //console.log(packageid);
            });
        });

        $('#send-comment').click(function(e){
            e.preventDefault();
            var packageid = $('#hiddenpackageid').val();
            var userid = $('#hiddenuserid').val();
            var comment = $('#comment-package').val();
            if (comment.trim()) {
                $.ajax({
                    type: "POST",
                    url: "/packages/ajax/sendcomment",
                    data: { packageid: packageid, userid: userid, comment: comment },
                    cache: false,
                    success: function(data){
                        var html = '';
                        for (var i = 0; i < data.comments.length; i++) {
                            var comment = data.comments[i];

                            html += '<div class="pull-left comment-content">';
                            html += '<div class="image-wrap">';
                            html += '<img src="'+ comment.image +'" alt="">'
                            html += '</div>';
                            html += '<div class="pull-left content-text-wrap">';
                            html += '<p><b>'+ comment.fullname +'</b> ' +  comment.comment + '</p>';
                            html += '<p class="font-sm datetime-format" data-messagetime="' + comment.created.date + '">'+ comment.created.date +'</p>';
                            html += '</div>';
                            html += '</div>';
                        }
                        $('#package-' + packageid).text(data.count);
                        $('.comment-list').html(html);
                        console.log(data.debug);
                        $('.datetime-format').each(function() {
                            var dateTime = $(this).data('messagetime');
                            //$(this).text(DateFormat.format.prettyDate(dateTime));
                            $(this).text(moment(dateTime, 'YYYY-MM-DD HH:mm Z').fromNow());

                        });
                        $('#comment-package').val('');
                    }
                });
            }
            //console.log(packageid + " - " + userid + " " + comment);
            //$('#modal-comment').modal('hide'); 
        });        

        $('#btn-add-package').click(function(e){
            e.preventDefault();
            $('.add-package').modal('show');
        });
        
    });
</script>
<link rel="stylesheet" type="text/css" media="screen" href="/css/datepicker.css" /> 
<div id="content" class="content-profile">

    <div class="content-full">
  								
  		<div class="content-top">
            <div class="container pos-relative">
                @include('includes.profilesidebar')
                
            </div>
        </div>
        <div class="container">
            <div class="content-bottom">
				<div class="col-md-12">
                		<div class="row">
                        	<div class="col-xs-8 col-sm-6 col-md-6 packages-title">
                            <h4 class="spaced">Active Package(s)</h4>
                            </div>
                            
                             
                            
                            <div class="col-xs-4 col-sm-6 col-md-6 new-package">
                            @if( Auth::check() && $selectedUser->id == Auth::user()->id)
                            <a href="#" data-toggle="modal" data-target=".add-package" id="btn-add-package" class="btn btn-default pull-right"><i class="glyphicon glyphicon-plus-sign green"></i><b>  New Package</b></a>
                            @endif
                            </div>
                        </div>
                </div>
                        <!-- start active packages -->
                    	
                    	<?php 
						$isPublic = 0;
						$isFriend = false;
						
						$isPublic = $selectedUser->ispublic;
						
						if($isPublic == 0)
						{
                            if($selectedUser->friends()->count() > 0)
                            {
								
								foreach($selectedUser->friends()->get() as $friend)
								{
									if(Auth::check() && $friend->friendInfo->id == Auth::user()->id)
									{
										$isFriend = true;
									}
								}
							}
						} 
						
						
					if($isFriend == true or $isPublic == 1 or (Auth::check() && $selectedUser->id == Auth::user()->id)){
					
						
					if ($selectedUser->id)
					{
						$user_id = $selectedUser->id;
					}
					
					function packageInterval($endDate)
					{  
							$datetime1 = new DateTime();
							$datetime2 = new DateTime("@" . $endDate);
							$interval = $datetime1->diff($datetime2);
							
							$day =  $interval->format('%d');
							$hour =  $interval->format('%h');
							$minutes =  $interval->format('%i');
							
							$totalleft = '';
							if($day != 0)
								$totalleft = $day.'D ';
							if($hour != 0)
								$totalleft .= $hour.'H ';
							if($minutes != 0)
								$totalleft .= $minutes.'M';
								
							return $totalleft;//$interval->format('%d D %h H %i M');
					}
						
						
						
						$packages = Package::where('user_id','=', $user_id)
									->where('cancel','!=',1)
                                    ->where('ended', '>=', date("Y-m-d H:i:s"))
									->orderBy('created_at','DESC')
									->paginate(2);
						
						if($packages->count() != 0){
							$index=0;
							foreach ($packages as $package)								
							{								
								
						?>
				<div class="col-md-6">
            		<div class="panel panel-default package-wrap">
                        <div class="panel-body">
                            <div class="row package-head">
                                <h3><b>{{$package->title}}</b> <span class="label label-primary pull-right">ACTIVE</span></h3>
                                
                                <p class="font-sm">POSTED ON {{ strtoupper(date("j M, Y",strtotime($package->posted))) }} <span>ENDING IN 
                                <b>{{ packageInterval(strtotime($package->ended))}}</b></span></p>
                                <?php 
								$_payment = '';												
								foreach($package->payments as $payment)
								{
									
									if($_payment!='')
									{
										$_payment .= ', '.$payment->payment_name;
									}else
									{
										$_payment = $payment->payment_name;
									}	
								}											
								?>
                                <p class="font-sm grey pull-left mtop5">Payment available : <b>{{$_payment}}</b>                                           
                                </p>
                          	</div>
                            <div class="row">
                            	<div class="col-xs-8 col-sm-9 col-md-8 package-content">
                                	<div class="row package-tournament-list">
                                    	<table class="package-tournaments">
                                        		<tr>
                                                	<th class="spaced">Tournament</th>
                                                    <th class="spaced">Buy-In</th>
                                                </tr>
                                                <?php
                                                $turnaments = $package->turnaments;
												//setlocale(LC_MONETARY, 'en_US');
												$total_buyin =0;
												foreach($turnaments as $turnament)
												{
													$total_buyin .= $turnament->buyin;
												?>
                                                <tr>
                                                	<td><a target="_blank" href="http://www.sharkscope.com/#Find-Tournament//networks/{{$turnament->network}}/tournaments/{{$turnament->game_id}}" > {{$turnament->name}}</a></td>
                                                    <td>${{money_format('%(#10n', $turnament->buyin)}}</td>
                                                </tr>
                                              	<?php }?>
                                        </table>
                                    </div>
                                    <div class="row package-total-value">
                                    	
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                        	<div class="row">
                                            Facebook Share
                                        	</div>
                                        </div>
                                        
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                        	<div class="row">
                                        		<p class="font-sm">Total</p>
                                                <p><b>${{money_format('%(#10n', $package->total)}}</b></p>
                                                
                                        	</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4 col-sm-3 col-md-4" style="position:static;">
                                	<div class="row package-right">
                                		<p class="font-sm">Mark-up</p>
                                        <p><b>{{$package->markup}}</b></p>
                                        <p class="font-sm">Sold</p>
                                        <p><span class="blue"><b>{{$package->sellingPercent()}}%</b></span> of <b>50%</b></p>
                                        
                                        @if( Auth::check() && $selectedUser->id != Auth::user()->id)
                                        <!--	<a class="btn btn-primary btn-buy toggle" href="#" data-toggle="modal" data-target=".percent1{{$package->id}}"><span class="glyphicon glyphicon-shopping-cart"></span> Buy</a>
                                        -->
                                        <button data-toggle="modal" data-target=".percent1{{$package->id}}" class="btn btn-lg btn-buy-amount"  <?php if(($package->selling - $package->sellingPercent()) == 0) echo 'disabled'; ?>>BUY
                            			<span class="amount-value-sm"></span></button>
                                        @elseif( Auth::check() && $selectedUser->id == Auth::user()->id)
                                        	
                                            <a class="btn btn-danger" href="/packs/cancel/{{$package->id}}"><span class="glyphicon glyphicon-trash"></span> Cancel </a>
                                        @else
                                        <!-- 	<a class="btn btn-primary btn-buy toggle" href="#" data-toggle="modal" data-target=".percent1{{$package->id}}"><span class="glyphicon glyphicon-shopping-cart"></span> Buy</a>
                                       	-->
                                         <button data-toggle="modal" data-target=".percent1{{$package->id}}" class="btn btn-lg btn-buy-amount"  <?php if(($package->selling - $package->sellingPercent()) == 0) echo 'disabled'; ?>>BUY
                            			<span class="amount-value-sm"></span></button>
                                        
                                        @endif 	
                 
                                        <div class="modal fade percent1{{$package->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-xs">
                                                <div class="modal-content buy-amount-dialog">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title spaced">Confirm Your Purchase</h4>
                                                        </div>
                                                        <div class="panel-body">
                                                            {{ Form::open(array('url'=>'cart/order', 'class'=>'form-signin')) }}
                                                            <input type="hidden" id="seller_id" name="seller_id" value="{{ $package->user_id }}" />
                                                            <input type="hidden" id="package_id" name="package_id" value="{{ $package->id }}" />
                                                            <input type="hidden" id="package_name" name="package_name" value="{{ $package->title }}" />
                                                            <input type="hidden" id="selling_price" name="selling_price" value="" />
                                                                                      
                                                            <div class="col-md-12" style="border-right:solid 1px #ccc;">
                                                                <span class="font-sm">
                                                                    <b>Percentage</b>
                                                                </span>
                                                         <?php 
            											 
            											 											 
            											 
            											 	$soldPercent = $package->sellingPercent();
            												$sellPercent =  $package->selling;												
            												$availablePencent = $sellPercent - $soldPercent;												
            												$countX = $availablePencent/$package->button1;												
            												
            											 ?>
                                                         	<script type="text/javascript">
            												$( document ).ready(function() {
                
            													$("#selling").change(function () {
            														var percent = this.value;
            														var total = {{$package->total}};
            														var value = total * (percent/100);
            														$("#selling_price").val(value);
            														
            													});
            													
            													var percent = $("#selling").val();
            													var total = {{$package->total}};
            													var value = total * (percent/100);
            													$("#selling_price").val(value);
            													
            													//console.log( "ready!" );
            												});
                                                            </script>
                                                         
                                                           		<select id="selling" name="selling" class="form-control form-sm">
                                                                 @for ($x=1; $x<=$countX; $x++)                                                          
                                                                 	<option value="{{$package->button1 * $x}}">{{$package->button1 * $x}}% - ${{$package->total * (($package->button1 * $x)/100)}}</option>
                                                                  @endfor
                                                                </select>
                                                            </div>
                                                    
                                                            <div class="payment-by">
                                                                <div class="col-md-6" style="border-right:solid 1px #ccc;">
                                                                    <p class="font-sm"><b>Payment by</b></p>
                                                                                        
                                                                    <select class="form-control form-sm"  id="payment_method" name="payment_method">
                                                                        <option value="pokerstars">PokerStars</option>
                                                                        <option value="fulltilt">Fulltilt</option>
                                                                        <option value="888">888</option>
                                                                        <option value="partypoker">Party Poker</option>
                                                                         <option value="titanpoker">Titan Poker</option>
                                                                        <option value="ipoker">iPoker</option>
                                                                    </select>                                                 
                                                                     
                                                                                       
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="font-sm"><b>In Game Name</b></p>
                                                                    <input type="text" class="form-control form-sm" id="in_game_name"  name="in_game_name" required>
                                                                </div>
                                                            </div>
                                                            <div class="panel-footer">
                                                            	<button class="btn btn-primary spaced font-sm" type="submit">Pay &amp; Submit</button>
                                                                <button class="btn btn-grey spaced pull-right font-sm" type="button" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                            {{ Form::close() }}
                                                        </div>                      
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer pull-left">
                          	<div class="col-xs-10 col-sm-10 col-md-10">
                            	<div class="row">
                                    <p>Comments from Seller</p>
                                    <p>{{$package->notes}}</p>
                            	</div>
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 btn-comment-wrap">
                            	<a href="#" data-packageid="{{$package->id}}"  class="btn btn-default btn-comment pull-right">Comment<span class="badge-sm pull-right" id="package-{{$package->id}}">{{ $package->comments->count() }}</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                        <?php $index++;
							}
						}else{  ?>
                        	There is no package.							
						<?php } ?>
                    <!-- End active packages -->
                <div class="col-md-12">
            		<div class="row"> 
           				{{ $packages->links() }} 
           			</div>
                </div>
                <?php }else { ?>You are not authorized to access this page.<?php }?>	        
            </div>
        </div>
  		
    </div>	

</div>
    <!-- Comment Dialog Box -->
    <div class="modal fade comment-box" id="modal-comment" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content buy-amount-dialog">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title spaced">Comments</h4>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12" style="border-right:solid 1px #ccc;">
                            <div class="loading" style="text-align: center;margin-top: 20px;display:none">
                                <img src="{{url('/img/loading.GIF')}}" style="width:30px;height:30px;" >
                            </div>
                            <div class="comment-list">
                                
                            </div>
                            <div class="comment-box-area">
                                @if(Auth::check())
                                <span class="small">
                                    Comment on this
                                </span>
                                <textarea name="comment-package" id="comment-package" class="form-control" cols="3" required></textarea>
                                <input type="hidden" id="hiddenuserid" value="{{Auth::user()->id}}" />
                                @endif
                                <input type="hidden" id="hiddenpackageid" value="0" />
                            </div>
                        </div>
                        <div class="panel-footer">
                            @if(Auth::check())
                            <a href="#" id="send-comment" class="btn btn-primary font-sm">Comment</a>
                            @endif
                        </div>
                    </div>      
                </div>
            </div>
        </div>
    </div>

    <!-- Comment Box End -->
    
    <!-- Add Package Modal Start -->
    <div class="modal fade add-package" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content add-package-dialog">
                <div class="panel panel-default">
                 	<div class="panel-heading">
                    	<h4 class="panel-title spaced">Add New Package</h4>
                  	</div>
                    <div class="panel-body">        				
                        
                        {{--<ul class="nav nav-tabs" role="tablist">
                            <li class="tab-head active"><a href="#packagedetail" class="spaced" role="tab" data-toggle="tab">Package Details</a></li>
                        </ul>--}}
                        {{ Form::open(array('url'=>'packs/create', 'class'=>'form-signin', 'id'=>'form-package')) }}
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in" id="packagedetail">
                    			<div class="col-md-12 mtop20">
                                	
            						<div class="row">
                                		<div class="form-group col-md-7">
                                            <label>Package Name</label>
                                            <input class="form-control" name="title" id="title" placeholder="Enter Package Name" type="text" required>
                                        </div>
                                                                        
                                        <div class="form-group col-md-5">
                                            <label>List for</label>
                                            <select id="ended" name="ended" class="form-control">
                                                <option value="6">6 Hours</option>
                                                <option value="12">12 Hours</option>
                                                <option value="24">24 Hours</option>
                                                <option value="48">2 Days</option>
                                                <option value="72">3 Days</option>
                                                <option value="96">4 Days</option>
                                                <option value="120">5 Days</option>
                                                <option value="144">6 Days</option>
                                                <option value="168">7 Days</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr class="divider">
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <div class="loading-tournament" style="text-align: center;margin-top: 20px;display:none">
                                                <img src="{{url('/img/loading.GIF')}}" style="width:30px;height:30px;" >
                                            </div>
                                            <div class="search-tournament-wrapper">
                                                <div class="col-md-3">
                                                    <select id="select-network" class="form-control">
                                                        <option value="pokerstars">PokerStars</option>
                                                        <option value="partypoker">Party Poker</option>
                                                        <option value="ipoker">iPoker</option>
                                                        <option value="fulltilt">FullTilt</option>
                                                        <option value="adjarabet">AdjaraBet</option>
                                                    </select>    
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" id="datepicker-tournament" class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" id="search-tournament-keyword" class="form-control" placeholder="Tournament Name">
                                                </div>
                                                <div class="col-md-3">
                                                    <button class="btn btn-primary" id="search-tournament-button">Search</button>
                                                    <button class="btn btn-primary" id="search-tournament-button">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tournaments-table-wrapper">

                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-12">
                                            <a href="#" class="abc"><span class="glyphicon glyphicon-plus"></span>  Add Tournaments</a>
                                        </div>
                                    </div> -->
                                    <div class="loading" style="text-align: center;margin-top: 20px;display:none">
                                        <img src="{{url('/img/loading.GIF')}}" style="width:30px;height:30px;" >
                                    </div>
                                    <ul class="addtour-wrap">
                                    	<li>
                                            <div class="row">
                                                <div class="form-group col-md-5">
                                                    <label>Tournament Name</label>
                                           		</div>
                                                <div class="form-group col-md-2">
                                                    <label>Network</label>
                                                </div>
                                                <div class="form-group col-md-5">
                                                    <label>Buy In</label>
                                               </div>
                                            </div>
                                        </li>
                                        <!-- <li>
                                            <div class="row addtour">
                                                <div class="form-group col-md-7">
                                                   <input class="form-control" id="turnament[]" name="turnament[]" placeholder="Enter Tournament Name" type="text">
                                                   <input id="gameid[]" name="gameid[]" type="hidden">
                                                </div>
                                                <div class="form-group col-md-5">
                                                   <input class="form-control hitung" id="buyin[]" name="buyin[]" placeholder="Enter Buy In Amount" type="text">
                                                </div>
                                            </div>
                                            
                                        </li> -->
                                    </ul>
                                    
                     				<hr class="divider">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label>Selling</label>
                                            <input class="form-control"  id="add-selling" name="add-selling" placeholder="Enter Percentage to Sell" type="text" value="50" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Mark Up</label>
                                            <input class="form-control" id="markup" name="markup" placeholder="Enter Mark Up Value" type="text" value="1.5" />
                                        </div>
                                
                                        <div class="form-group col-md-3">
                                            <label>Selling Amount</label>
                                            <p class="amount-total"><input name="selling_amount" id="selling_amount" disabled="disabled" type="text" /></p>
                                            <input name="selling_amount_form" id="selling_amount_form" type="hidden" />
                                       		<span id="keterangan"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Total</label>
                                            <p class="amount-total"><input name="total" id="total" disabled="disabled" type="text" /></p>
                                            <input name="total_form" id="total_form" type="hidden" />
                                       		<span id="keterangan"></span>
                                        </div>
                                       
                                    </div>

                                    {{--<ul class="nav nav-tabs" role="tablist">
                                        <li class="tab-head active"><a href="#paymentmenthod" class="spaced" role="tab" data-toggle="tab">Payment Method</a></li>
                                    </ul>--}}
                                                        
                                    <div class="row">
									<a href="#paymentmenthod" class="spaced" role="tab" data-toggle="tab"></a>
								</div>                          
                                </div>     
                            </div>
                            <!-- Add Reserved -->
                            <div class="tab-pane fade active in" id="paymentmenthod">
								<div class="row">
									 <div class="col-sm-12 col-md-12">
                                        	<label><b>Payment Available by :</b><label>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                    		<div class="checkbox">
                                                <label>
                                                  <input type="checkbox" id="payment[]" name="payment[]" class="checkbox-payment" value="Pokerstars"/> Pokerstars
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                    		<div class="checkbox">
                                                <label>
                                                  <input type="checkbox" id="payment[]" name="payment[]" class="checkbox-payment" value="Fulltilt"/> Fulltilt
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                    		<div class="checkbox">
                                                <label>
                                                  <input type="checkbox" id="payment[]" name="payment[]" class="checkbox-payment" value="888"/> 888
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                    		<div class="checkbox">
                                                <label>
                                                  <input type="checkbox" id="payment[]" name="payment[]" class="checkbox-payment" value="Party Poker"/> Party Poker
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                    		<div class="checkbox">
                                                <label>
                                                  <input type="checkbox" id="payment[]" name="payment[]" class="checkbox-payment" value="Titan Poker"/> Titan Poker
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <label><b>Check the currency:</b><label>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <div class="radio">
                                                <label>
                                                  <input type="radio" id="moneyoption" name="moneyoption" class="radio-moneyoption" value="usd"/> USD
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <div class="radio">
                                                <label>
                                                  <input type="radio" id="moneyoption" name="moneyoption" class="radio-moneyoption" value="euro"/> Euro
                                                </label>
                                            </div>
                                        </div>
								</div>

                                {{--<ul class="nav nav-tabs" role="tablist">
                                    <li class="tab-head active"><a href="#summary" class="spaced" role="tab" data-toggle="tab">Summary</a></li>
                                </ul>--}}

								<div class="row">
									<a href="#summary" class="spaced" role="tab" data-toggle="tab"></a> <a href="#summary" class="spaced" role="tab" data-toggle="tab"></a>
								</div>
                            </div>
                            <!-- Add Reserved End -->
							
							 <div class="tab-pane fade active in" id="summary">
								 <hr class="divider">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label>Note</label>
                                            <textarea class="form-control" rows="3" id="notes" name="notes" placeholder="Enter Note for Package"></textarea>
                                        </div>
                                    </div>
                                    <hr class="divider">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Button 1</label>
                                            <input class="form-control" id="button1" name="button1" placeholder="Enter Percentage Value" type="text" required />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Button 2</label>
                                            <input class="form-control" id="button2" name="button2" placeholder="Enter Percentage Value" type="text" required />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Button 3</label>
                                            <input class="form-control" id="button3" name="button3" placeholder="Enter Percentage Value" type="text" required />
                                        </div>
                                    </div>
                                    <input type="hidden" name="user_id" id="user_id" value="<?php if(Auth::check()) echo Auth::user()->id; ?>" />
                                   
									<a href="#paymentmenthod" class="spaced" role="tab" data-toggle="tab"></a>
								<!-- <button class="btn btn-primary" id="submit-package" type="submit">Submit Package</button> -->
                                <div class="row margtop20">
                    <div class="col-xs-12 col-md-12">
                     {{ Form::submit('Submit Package', array('class'=>'btn btn-primary btn-block btn-lg'))}}
                    </div> 
                    <!-- <div class="col-xs-6 col-md-6"><input type="submit" value="Register" class="btn btn-primary btn-block btn-lg" tabindex="7"></div>
                    <div class="col-xs-6 col-md-6"><a href="#" class="btn btn-success btn-block btn-lg">Sign In</a></div> -->
                    <!-- <div class="col-xs-6 col-md-6">
                        <a href="/sign-in-with-facebook" class="btn btn-block btn-social btn-facebook"><i class="fa fa-facebook"></i>Register with Facebook</a>
                    </div>  -->
                </div>
                                              
                            </div>
                        </div>
          		    </div>
                    
                </div>
				 {{ Form::close() }}        		
				 <!-- XXX-->
			</div>
        </div>
    </div>
    <script type="text/javascript" src="/js/jquery_.js"></script>
    
    
    <script type="text/javascript">
	jQuery.noConflict();
	$(function(){
        
		var i = 1;
		$(".abc").click(function() {
			$(".addtour-wrap li:nth-child(2)").clone().find("input").each(function() {
				$(this).val('');
			}).end().appendTo(".addtour-wrap");
			i++;
			
			jQuery('.hitung').keyup(function () { 
				callBuyin();
			 });
		});
		 
		function callBuyin(){
			var buyins = document.getElementsByName('buyin[]');
			var totalbuyin = 0;
			
			for (var i=0; i<buyins.length; i++)
			{
				totalbuyin += Number(buyins[i].value);
			}
			var selling = Number(jQuery('#add-selling').val());
			var sellingP = (selling/100);
			var total =  0;//(totalbuyin * sellingP) ;
			
			//alert(sellingP);
			
			var markup = 0;
			if(jQuery('#markup').val()=='') markup = 0;
			else markup = jQuery('#markup').val();
			
			totalmarkup = totalbuyin * markup;
			
			selling_total = totalmarkup * sellingP;
			
			total = totalbuyin * markup;//(totalbuyin+selling_total)-(totalbuyin*sellingP);
			//jQuery('#keterangan').html('selling='+selling+' , selling % ='+ sellingP + ' , markup = '+ totalmarkup +' , totalbuyin = '+totalbuyin + ', total = '+ total);
						
			jQuery('#selling_amount').val(selling_total); 
			jQuery('#selling_amount_form').val(selling_total);
			
			jQuery('#total').val(total); 
			jQuery('#total_form').val(total);
			
		}
		 
		 jQuery('#selling').keyup(function () { 
		 	callBuyin();
		 });
		 
		 jQuery('#markup').keyup(function () { 
		 	callBuyin();
		 });
		 
		 jQuery('.hitung').keyup(function () { 
		 	callBuyin();
		 });
		 
		 jQuery('#form-package').bootstrapValidator({
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				'payment[]': {
					validators: {
						choice: {
							min: 1,
							max: 5,
							message: 'Please choose payment you are good at'
						}
					}
				}
			}
		});
     });
    </script>
    <!-- Add Package Modal End -->

@stop