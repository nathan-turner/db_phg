// ******************************************************************************
// script for view/pinnacle/mail/index.phtml

var murl = "";
var mlurl = "";
var mlform = null;
var mlconfirm = null;

function mlMainSubmit() {
    var x=$("#maildesc").serializeArray();
    var y = {};
    $.each(x, function(i, field){
      y[field.name] = field.value;
    });
    var url = murl + "edit/" + y.list_id;
    $.post(url, y, function(result, status) {
        if( status == "success" && result.code == 200 ) {
            if( result.list_id ) {
                var lid = result.list_id
                var s = '<td><a href="'+mlurl+lid+'/1'
                s += '">'+lid+' </a></td> <td>'+result.name+'</td><td>'+result.description+'</td><td>(Updated)</td> '
                s += '<td><a href="#" data-list-id="'+lid+'" class="my-listedit btn btn-small"><i class="icon-pencil"></i> Edit</a> '
                s += '<a href="#" data-list-id="'+lid+'" class="my-listdel btn btn-small btn-danger"><i class="icon-trash icon-white"></i> Delete</a> </td></tr>'
                $("#my-row-"+lid).html(s);
                $(".my-listedit").off("click").click(mlMainEdit);
                $(".my-listdel").off("click").click(mlMainDel);
            }
        }
        $("#my-row-form").remove();
    });
}

function mlMainDelYes() {
    var y = { 'confirm': 'jawohl' };
    var lid = $(this).data('listId');
    var url = murl + "del/" + lid;
    $.post(url, y, function(result, status) {
        if( status == "success" && result.code == 200 ) {
            if( lid == result.list_id ) {
                $("#my-row-"+lid).remove();
            }
        }
    });
    $("#my-confirm-div").remove();
}

function mlMainDel() {
    var lid = this.dataset.listId;
    $("#my-confirm-div").remove();
    $("#my-row-form").remove();
    $(mlconfirm).clone().insertAfter(this);
    $('<br>').insertAfter(this);
    $("#my-confirm-div").removeClass("hidden");
    $("#my-confirm-yes").data('listId',lid);
    $("#my-confirm-yes").click(mlMainDelYes);
    $("#my-confirm-no").click(function(){
        $("#my-confirm-div").remove();
    });
}

function mlMainEdit() {
    var lid = this.dataset.listId;
    var s = '<tr id="my-row-form"> <td> </td> <td colspan="4" id="my-col-form"></td></tr>'
    $("#my-confirm-div").remove();
    $("#my-row-form").remove();
    $("#my-row-"+lid).after(s);
    $(mlform).clone().appendTo("#my-col-form");
    $("#my-list").val(lid);
    $("#my-name").val( $("#my-row-"+lid+" td:eq(1)").text() );
    $("#my-desc").val( $("#my-row-"+lid+" td:eq(2)").text() );
    $("#my-form-div").removeClass("hidden");
    $("#submitbutton").click(mlMainSubmit);
    $(".close").click(function() {
        $("#my-row-form").remove();
    });
}

function mlMainAdd() {
    var y = { name: $("#my-new-name").val() };
    var url = murl + "add/0";
    if( y.name == '' ) alert('Please provide list name')
    else $.post(url, y, function(result, status) {
        if( status == "success" && result.code == 200 ) {
            var lid = result.list_id;
            if( lid ) {
                var s = '<tr id="my-row-'+lid+'"> <td><a href="'+mlurl+lid+'/1'
                s += '">'+lid+' </a></td> <td>'+result.name+'</td><td> </td><td>(New)</td> '
                s += '<td><a href="#" data-list-id="'+lid+'" class="my-listedit btn btn-small"><i class="icon-pencil"></i> Edit</a> '
                s += '<a href="#" data-list-id="'+lid+'" class="my-listdel btn btn-small btn-danger"><i class="icon-trash icon-white"></i> Delete</a> </td></tr>'
                $("#my-tbody").prepend(s);
                $("#my-new-name").val('');
                $(".my-listedit").off("click").click(mlMainEdit);
                $(".my-listdel").off("click").click(mlMainDel);
            }
        }
    });
}

function mlMainInit( lurl, url, redir ) {
    murl = url; mlurl = lurl;
    $("#searchstatus").ajaxError(function(e,xhr,opt) {
        if( xhr.status == 403 && confirm("Your session was closed (time out): please log in!") ) {
            window.location = redir;
        }
        else alert("Error requesting " + opt.url + ": " + xhr.status + " " + xhr.statusText);
    });

    $(document).ready(function(){
      mlform = $("#my-form-div").detach();
      mlconfirm = $("#my-confirm-div").detach();
      $("#searchstatus").ajaxStart(function(){
            $("#searchstatus").html('<span class="label label-info">Loading... Please wait</span>');
      }).ajaxComplete(function(){
            $("#searchstatus").text('');
      });
      $(".my-listedit").click(mlMainEdit);
      $(".my-listdel").click(mlMainDel);
      $("#my-list-add").click(mlMainAdd);
    });

}

// ******************************************************************************
// script for view/pinnacle/mail/list.phtml

function mlListInit( sess /*, url, redir*/ ) {
    /*murl = url;*/ sessch = sess;
    /* *** no ajax on this page
    $("#searchstatus").ajaxError(function(e,xhr,opt) {
        if( xhr.status == 403 && confirm("Your session was closed (time out): please log in!") ) {
            window.location = redir;
        }
        else alert("Error requesting " + opt.url + ": " + xhr.status + " " + xhr.statusText);
    });
    *** */

    $(document).ready(function(){
        /* *** no ajax on this page
        $("#searchstatus").ajaxStart(function(){
            $("#searchstatus").html('<span class="label label-info">Loading... Please wait</span>');
        }).ajaxComplete(function(){
            $("#searchstatus").text('');
        });
        *** */
        $("#my-resform").submit(submitch);
        $("#my-uall").click(ckall);
        markch();
        $(".my-ckbox").change(function(){
            chanch(this.id,this.checked);
        });
    });

}

var cktoggle = true;
var sessch = ";";

function submitch() {
  try {
  	var ro = document.getElementById("chactdel");
	var s = sessionStorage.resultsch;
	var chma = document.getElementById("chactsmany");
	if( chma.checked ) {
		var s2 = s.replace(/;/g,",");
		s2 = s2.replace(/ch_/g,"");
		var cach = document.getElementById("sesscach");
		cach.value = s2;
	        // alert("OK "+s2);
	}
  	if( s && ro.checked ) {
  	    // clean resulthsch off marked boxes
	    for(var i = 0; i < document.resform.elements.length; i++) {
	        var o = document.resform.elements[i];
	        if( o.tagName == "INPUT" && o.type == "checkbox" && o.checked )
	           s = s.replace(";"+o.id+";",";");
	    }
	    sessionStorage.resultsch = s;
	}
  } catch (e) {
  	// sessionStorage is not supported: too bad
  } finally {}
  return true;
}

function markch() {
  try {
	if( !sessionStorage.resultsch) sessionStorage.resultsch= sessch;
	var smy = sessionStorage.resultsch.split(";");
	for (var i=1;i<smy.length-1;i++) {
	    var o = document.getElementById(smy[i]);
	    if( o ) o.checked = true;
	}
  } catch (e) {
  	// sessionStorage is not supported: too bad
  } finally {}
  return true;
}

function chanch(oid,state) {
  try {
	var s = sessionStorage.resultsch;
	var i = s.indexOf(";"+oid+";");
	if( state && i<0 ) { // add
	    sessionStorage.resultsch = s + oid + ";";
	}
	else if( !state && i>=0 ) { // remove
	    sessionStorage.resultsch = s.replace(";"+oid+";",";");
	}
	// document.getElementById("debug").value=sessionStorage.resultsch;
  } catch (e) { 
  	// sessionStorage is not supported: too bad
  } finally {}
  return true;
}

function ckall() {
    for(i = 0; i < document.resform.elements.length; i++) {
        var o = document.resform.elements[i];
        if( o.tagName == "INPUT" && o.type == "checkbox" ) { 
        	o.checked = cktoggle;
        	chanch(o.id, cktoggle);
        }
    }
    cktoggle = !cktoggle;
    return true;
}

