jQuery(document).ready(function(){

	var apiurl = 'https://api.ink.moe/mcme/';
	
	function getCommentCookie(Name)
	{
		var search = Name + "=" ;
		if(document.cookie.length > 0)
		{
			var offset = document.cookie.indexOf(search);
			if(offset != -1)
			{
				offset += search.length;
				end = document.cookie.indexOf(";", offset);
				if(end == -1) end = document.cookie.length;
				return unescape(document.cookie.substring(offset, end));
			}
			else return "NE";
		} else return "NE";
	}

	var currentPage = 1;
	var maxPage = 1;
	var pageId = $('#imArticleID').val();
	var commentToken = getCommentCookie("comment_token");
	var logIn = false;
	var tokenInfo;
	var cache = new Array(0);
	var onLoadingLock = false;

	function showHint(hintString){
		$("#comment-hint").fadeIn(300);
		$("#comment-hint").html(hintString);
	}

	function hideHint(){
		setTimeout(function() {
        	$("#comment-hint").fadeOut(1000);
	    }, 5000);
	}

	function outputInsertComment(theCmt){
		var username;
		var imgHTML = "<img src=\""+apiurl+"avatar-comment.php\">";
		if (theCmt.user_name == null) {
			username = theCmt.account_name;
			imgHTML = "<img src=\""+apiurl+"avatar-comment.php?a="+username+"\">";
		} else{
			username = theCmt.user_name;
		};
		var titleHTML = "<span class=\"cmt-username\">"+username+"</span>";
		var cmtHTML = "<p>"+theCmt.comment+"</p>";
		if (theCmt.parent_id==0) {
			$("#comment-startflag").after("<div class=\"comment-contentbox\"><div class=\"comment-leftbox\">"+imgHTML+"</div><div class=\"comment-rightbox\" id=\"cmt-target-"+theCmt.id+"\">"+titleHTML+cmtHTML+"<div class=\"cmt-ctrlbox\"><span>"+theCmt.submit_time+" ［<a href=\"#\" id=\"cmt-reply-"+theCmt.id+"\">回复</a>］</span></div><div id=\"cmt-replybox-"+theCmt.id+"\"></div></div></div>");
		} else{
			$("#cmt-replybox-"+theCmt.parent_id).after("<div class=\"comment-contentbox\"><div class=\"comment-leftbox\">"+imgHTML+"</div><div class=\"comment-rightbox\" id=\"cmt-target-"+theCmt.id+"\">"+titleHTML+cmtHTML+"<div class=\"cmt-ctrlbox\"><span>"+theCmt.submit_time+" ［<a href=\"#\" id=\"cmt-reply-"+theCmt.id+"\">回复</a>］</span></div><div id=\"cmt-replybox-"+theCmt.id+"\"></div></div></div>");
		};
		$(".comment-contentbox").fadeIn(300);
	}

	function outputSingleComment(theCmt){
		var username;
		var imgHTML = "<img src=\""+apiurl+"avatar-comment.php\">";
		if (theCmt.user_name == null) {
			username = theCmt.account_name;
			imgHTML = "<img src=\""+apiurl+"avatar-comment.php?a="+username+"\">";
		} else{
			username = theCmt.user_name;
		};
		var titleHTML = "<span class=\"cmt-username\">"+username+"</span>";
		var cmtHTML = "<p>"+theCmt.comment+"</p>";
		if (theCmt.parent_id==0) {
			$("#comment-endflag").before("<div class=\"comment-contentbox\"><div class=\"comment-leftbox\">"+imgHTML+"</div><div class=\"comment-rightbox\" id=\"cmt-target-"+theCmt.id+"\">"+titleHTML+cmtHTML+"<div class=\"cmt-ctrlbox\"><span>"+theCmt.submit_time+" ［<a href=\"#\" id=\"cmt-reply-"+theCmt.id+"\">回复</a>］</span></div><div id=\"cmt-replybox-"+theCmt.id+"\"></div></div></div>");
		} else{
			cache.push(theCmt);
		};
	}

	function outputChildComment(theCmt,theIndex){
		var username;
		var imgHTML = "<img src=\""+apiurl+"avatar-comment.php\">";
		if (theCmt.user_name == null) {
			username = theCmt.account_name;
			imgHTML = "<img src=\""+apiurl+"avatar-comment.php?a="+username+"\">";
		} else{
			username = theCmt.user_name;
		};
		var titleHTML = "<span class=\"cmt-username\">"+username+"</span>";
		var cmtHTML = "<p>"+theCmt.comment+"</p>";

		var did = false;

		$("#cmt-replybox-"+theCmt.parent_id).after(function(){
			did = true;
			return "<div class=\"comment-contentbox\"><div class=\"comment-leftbox\">"+imgHTML+"</div><div class=\"comment-rightbox\" id=\"cmt-target-"+theCmt.id+"\">"+titleHTML+cmtHTML+"<div class=\"cmt-ctrlbox\"><span>"+theCmt.submit_time+" ［<a href=\"#\" id=\"cmt-reply-"+theCmt.id+"\">回复</a>］</span></div><div id=\"cmt-replybox-"+theCmt.id+"\"></div></div></div>";
		});

		if (did) {
			cache.splice(theIndex,1);
		};

	}


	function getComment(){
		if (currentPage <= maxPage) {
			console.log("loading");
			currentPage ++;
			$.ajax({
				type:"GET",
				dataType:"html",
				data:{
					'id':pageId,
					'cp':currentPage-2
				},
				url: apiurl+'get-comment.php',
				success:function(msg){
					var data = msg.split('|');
					if (data[0]=="no comment") {
						$("#imCommentBox span").html("暂无评论");
						$("#comment-loadmore span").html("快来抢沙发！");
					} else{
						if (data[0]=="error") {
							$("#imCommentBox span").html("评论获取失败");
							$("#comment-loadmore span").html("服务器错误，请联系管理员");
						} else{
							var obj = eval ("(" + data[2] + ")");

							maxPage = parseInt(data[1]);
							$("#imCommentBox span").html("共有"+data[0]+"条评论");
							if (currentPage == maxPage) {
								$("#comment-loadmore span").html("正在加载最后一页...");
							}else if(currentPage > maxPage){
								$("#comment-loadmore span").html("所有评论已经加载完毕！");
							}else{
								$("#comment-loadmore span").html("正在加载下一页 （"+currentPage+"/"+data[1]+"）...");
							};

							for (var i =  0; i < obj.length; i++) {
								outputSingleComment(obj[i]);
							};

							for (var j = cache.length - 1; j > -1 ; j--) {
								outputChildComment(cache[j],j);
							};
							$(".comment-contentbox").fadeIn(300);
							attachCommentBox();
							onLoadingLock = false;
						};
					};
				}
			});
		};
	}

	function attachCommentBox(){
		$('[id^="cmt-reply-"]').unbind();
		$('[id^="cmt-reply-"]').click(function(){
			var cid = $(this).attr('id').substr(10);
			if ($("#cmt-replybox-"+cid).html()=="") {
				drawCommentBox(cid);
			} else{
				$("#cmt-replybox-"+cid).toggle(300);
			};
			return false;
		});
	}


	function drawCommentBox(pid){
		if (pid == 0) {
			if (logIn) {
				$("#imCommentBox").after("	<div class=\"comment-submitbox\">		<textarea class=\"comment-textarea\" id=\"cmt-textarea-p"+pid+"\"></textarea>		<div class=\"cmt-submit-field\">			<input type=\"button\" class=\"cmt-submit\" value=\"提交评论\" pid=\""+pid+"\" aid=\""+pageId+"\"/>		</div>		<div class=\"cmt-blankbox\"></div>	</div>");
			} else{
				$("#imCommentBox").after("	<div class=\"comment-submitbox\">		<textarea class=\"comment-textarea\" id=\"cmt-textarea-p"+pid+"\"></textarea>		<div class=\"cmt-text-field\">			<span>昵称: </span><input type=\"text\" class=\"cmt-name\"  id=\"cmt-name-p"+pid+"\"/>		</div>		<div class=\"cmt-text-field\">			<span>邮箱: </span><input type=\"text\" class=\"cmt-email\"  id=\"cmt-email-p"+pid+"\"/>		</div>		<div class=\"cmt-submit-field\">			<input type=\"button\" class=\"cmt-submit\" value=\"提交评论\" pid=\""+pid+"\" aid=\""+pageId+"\"/>		</div>		<div class=\"cmt-blankbox\"></div>	</div>");
			};
		} else{
			if (logIn) {
				$("#cmt-replybox-"+pid).html("	<div class=\"comment-submitbox\" id=\"cmtchildbox-"+pid+"\" style=\"display:none;\">		<textarea class=\"comment-textarea\" id=\"cmt-textarea-p"+pid+"\"></textarea>		<div class=\"cmt-submit-field\">			<input type=\"button\" class=\"cmt-submit\" value=\"提交评论\" pid=\""+pid+"\" aid=\""+pageId+"\"/>		</div>		<div class=\"cmt-blankbox\"></div>	</div>");
			}else{
				$("#cmt-replybox-"+pid).html("	<div class=\"comment-submitbox\" id=\"cmtchildbox-"+pid+"\" style=\"display:none;\">		<textarea class=\"comment-textarea\" id=\"cmt-textarea-p"+pid+"\"></textarea>		<div class=\"cmt-text-field\">			<span>昵称: </span><input type=\"text\" class=\"cmt-name\"  id=\"cmt-name-p"+pid+"\"/>		</div>		<div class=\"cmt-text-field\">			<span>邮箱: </span><input type=\"text\" class=\"cmt-email\"  id=\"cmt-email-p"+pid+"\"/>		</div>		<div class=\"cmt-submit-field\">			<input type=\"button\" class=\"cmt-submit\" value=\"提交评论\" pid=\""+pid+"\" aid=\""+pageId+"\"/>		</div>		<div class=\"cmt-blankbox\"></div>	</div>");
			};
			$("#cmtchildbox-"+pid).show(300);
		};
		$(".cmt-submit").unbind();
		$(".cmt-submit").click(function(){
			submitComment($(this).attr("pid"),$(this).attr("aid"));
		});
	}

	function submitComment(pid, aid){
		showHint("评论提交中...");
		$(".cmt-submit[pid="+pid+"]").attr('disabled','disabled');
		if (logIn) {
			$.ajax({
				type:"POST",
				dataType:"html",
				data:{
					'pageid':aid,
					'parentid':pid,
					'comment':$("#cmt-textarea-p"+pid).val(),
					'uid':tokenInfo[0],
					'token':tokenInfo[1]
				},
				url: apiurl+'submit-comment.php',
				success:function(msg){
					var data = msg.split('|');
					if (data[0]=="success") {
						showHint("提交成功！");
						$("#cmt-textarea-p"+pid).val('');
						$(".cmt-submit[pid="+pid+"]").removeAttr('disabled');
						hideHint();
						var obj = eval ("(" + data[1] + ")");
						outputInsertComment(obj);
					}else{
						showHint("[错误]"+data[0]);
						$(".cmt-submit[pid="+pid+"]").removeAttr('disabled');
						hideHint();
					};
				},
				error:function(){
					showHint("提交失败！");
					$(".cmt-submit[pid="+pid+"]").removeAttr('disabled');
					hideHint();
				}
			});
		} else{
			$.ajax({
				type:"POST",
				dataType:"html",
				data:{
					'pageid':aid,
					'parentid':pid,
					'comment':$("#cmt-textarea-p"+pid+"").val(),
					'name':$("#cmt-name-p"+pid+"").val(),
					'email':$("#cmt-email-p"+pid+"").val()
				},
				url: apiurl+'submit-comment.php',
				success:function(msg){
					var data = msg.split('|');
					if (data[0]=="success") {
						showHint("提交成功！");
						$("#cmt-textarea-p"+pid).val('');
						$(".cmt-submit[pid="+pid+"]").removeAttr('disabled');
						hideHint();
						var obj = eval ("(" + data[1] + ")");
						outputInsertComment(obj);
					}else{
						showHint("[错误]"+data[0]);
						$(".cmt-submit[pid="+pid+"]").removeAttr('disabled');
						hideHint();
					};
				},
				error:function(){
					showHint("提交失败！");
					$(".cmt-submit[pid="+pid+"]").removeAttr('disabled');
					hideHint();
				}
			});
		};
	}

	if (commentToken != "NE") {
		logIn = true;
		tokenInfo = commentToken.split('|');
	};

	drawCommentBox(0);
	$("#comment-endflag").after("<div id=\"comment-loadmore\"><span>加载中...</span></div>");
	getComment();

	$(window).scroll(function(){
		totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()) + 200;
		if($(document).height() <= totalheight){
			if(onLoadingLock==false){
				onLoadingLock = true;
				getComment();
			}
		}
	});



});
