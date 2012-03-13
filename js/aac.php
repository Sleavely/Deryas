<?php 

//Tell everyone that this is a javascript, we dont want it confused with HTML
header("content-type: application/x-javascript");
$navcat = $_REQUEST['navcat'];

switch($_REQUEST['subtopic']){
    case "accountmanagement":
        $special = '
                    //ajax forms without form tags
                    $(".editaccinfo").live("keyup", function(event) {
                        if (event.which == 13) { //instead of keyCode or charCode we use .which because it contains both (i suppose you could call it jquery-exclusive)
                            $(this).children("a").click();
                            return false;
                        }
                    });

                    //char-create stuff
                    var showingcharlist = true;
                    var showingcharcreateform = false;
                    $(".viewcharcreatebutton").live("click", function() {
                        if (showingcharlist == true){
                            $("#characterlist").hide("blind", { direction: "vertical" }, 1000);
                            $("#charcreateform").show("blind", { direction: "vertical" }, 1000);
                            showingcharlist = false;
                        }else{
                            $("#charcreateresponse").hide("blind", { direction: "vertical" }, 1000);
                            $("#charcreateform").show("blind", { direction: "vertical" }, 1000);
                        }
                        showingcharcreateform = true;
                    });
                    $(".hidecharcreatebutton").live("click", function() {
                        if (showingcharlist == false){
                            if (showingcharcreateform == true){
                                $("#charcreateform").hide("blind", { direction: "vertical" }, 1000);
                                $("#characterlist").show("blind", { direction: "vertical" }, 1000);
                                showingcharcreateform = false;
                                showingcharlist = true;
                            }else{
                                //this is #charcreateresponsebutton
                                if($("#charcreateresponsebutton").hasClass("negative")){
                                    $("#charcreateresponse").hide("blind", { direction: "vertical" }, 1000);
                                    $("#charcreateform").show("blind", { direction: "vertical" }, 1000);
                                    showingcharcreateform = true;
                                }else{
                                    $("#charcreateresponse").hide("blind", { direction: "vertical" }, 1000);
                                    $("#characterlist").show("blind", { direction: "vertical" }, 1000);
                                    showingcharlist = true;
                                }
                            }
                        }
                    });
                    $("#proceedcharcreatebutton").live("click", function() {
                        var newcharname = $("#newcharname").val();
                        var newchargender = $("input:radio[name=newchargender]:checked").val();
                        var newcharvoc = "";
                        var newchartown = "";
                        //check if voc/town dropdowns exist
                        if($("#newcharvoc").length) newcharvoc = $("#newcharvoc").val();
                        if($("#newchartown").length) newchartown = $("#newchartown").val();
                        $.post("ajax.php?subtopic=accountmanagement&action=addcharacter", { "newcharname": newcharname, "newchargender": newchargender, "newcharvoc": newcharvoc, "newchartown": newchartown },
                            function(data){
                                if(data.success == 1){
                                    $("#charcreateresponsebutton").removeClass("negative");
                                    $("#charcreateresponsetext").html("Your character has been created.");
                                    $("#newcharname").val("");
                                    $("#userdatacharacters").animate({opacity: 0.1}, 400, function() {
                                        // Animation complete.
                                        $("#userdatacharacters").html("<span>Characters:</span> " + data.totalcharacters);
                                        $("#userdatacharacters").animate({opacity: 1.0}, 400);
                                    });
                                    $("#characterlist").append(""
                    +"<div class=\"charbox\">"
                        +"<div class=\"charname\">"
                            + data.name
                        +"</div>"
                        +"<div class=\"chardesc\">"
                            +"Level " + data.level + " " + data.vocation
                        +"</div>"
                        +"<div class=\"charbuttons\">"
                            +"<a href=\"?subtopic=accountmanagement&action=editcharacter&name=" + data.urlname + "\" class=\"abutton\">"
                                +"<img src=\"images/icons/pencil.png\" alt=\"\"/>"
                                +"Edit"
                            +"</a>"
                            +"<a href=\"?subtopic=accountmanagement&action=deletecharacter&name=" + data.urlname + "\" class=\"abutton negative\">"
                                +"<img src=\"images/icons/delete.png\" alt=\"\"/>"
                                +"Delete"
                            +"</a>"
                        +"</div>"
                    +"</div>");
                                }else{
                                    $("#charcreateresponsebutton").addClass("negative");
                                    $("#charcreateresponsetext").html("Failure: " + data.errormsg);
                                }
                            },
                        "json");
                        $("#charcreateform").hide("blind", { direction: "vertical" }, 1000);
                        $("#charcreateresponse").show("blind", { direction: "vertical" }, 1000);
                        showingcharcreateform = false;
                    });

                    //Change Password
                    $("#changepwdbtn").live("click", function() {
                        var changepwdtxtone = $("#changepwdtxtone").val();
                        var changepwdtxttwo = $("#changepwdtxttwo").val();
                        $.post("ajax.php?subtopic=accountmanagement&action=changepassword", { "changepwdtxtone": changepwdtxtone, "changepwdtxttwo": changepwdtxttwo },
                            function(data){
                                if(data.success == 1){
                                    //update #userdatapassword
                                    $("#userdatapassword").html("<span>Password Age:</span> Less than a day");
                                    $(\'#changepwdform\').toggle(400);
                                    $("#changepwdtxtone").val("");
                                    $("#changepwdtxttwo").val("");
                                }else{
                                    alert("Fail: " + data.errormsg);
                                }
                            },
                        "json");
                    });
					
					//Change Secret Question
                    $("#changesecretquestionbtn").live("click", function() {
                        var changequestiontxt = $("#changequestiontxt").val();
                        var changeanswertxt = $("#changeanswertxt").val();
                        $.post("ajax.php?subtopic=accountmanagement&action=changesecretquestion", { "changequestiontxt": changequestiontxt, "changeanswertxt": changeanswertxt },
                            function(data){
                                if(data.success == 1){
                                    //update #userdatasecretquestion
                                    $("#userdatasecretquestion").html("<span>Secret Question:</span> " + data.newquestion);
                                    $(\'#changesecretquestionform\').toggle(400);
                                    $("#changequestiontxt").val("");
                                    $("#changeanswertxt").val("");
                                }else{
                                    alert("Fail: " + data.errormsg);
                                }
                            },
                        "json");
                    });

                    //Change Real Name
                    $("#changenamebtn").live("click", function() {
                        var changenametxt = $("#changenametxt").val();
                        $.post("ajax.php?subtopic=accountmanagement&action=changerealname", { "changenametxt": changenametxt },
                            function(data){
                                if(data.success == 1){
                                    //change #userdatarealname
                                    $("#userdatarealname").html("<span>Real Name:</span> " + data.newname);
                                    $(\'#changenameform\').toggle(400);
                                }else{
                                    alert("Fail: " + data.errormsg);
                                }
                            },
                        "json");
                    });

                    //Change Email
                    $("#changemailbtn").live("click", function() {
                        var changemailtxtone = $("#changemailtxtone").val();
                        var changemailtxttwo = $("#changemailtxttwo").val();
                        $.post("ajax.php?subtopic=accountmanagement&action=changeemail", { "changemailtxtone": changemailtxtone, "changemailtxttwo": changemailtxttwo },
                            function(data){
                                if(data.success == 1){
                                    //update #userdataemail
                                    $("#userdataemail").html("<span>Email:</span> " + changemailtxtone);
                                    $(\'#changemailform\').toggle(400);
                                    $("#changemailtxtone").val("");
                                    $("#changemailtxttwo").val("");
                                }else{
                                    alert("Fail: " + data.errormsg);
                                }
                            },
                        "json");
                    });
					
					
                    ';
        break;
    case "admin":
        if (!isset($_REQUEST["action"])) $_REQUEST["action"] = 'heltRandomString';
        switch($_REQUEST["action"]){
            case "news":
                $special = '$("a.addnewsbtn").live("click", function() {
                                $("#addnewsdiv").show("blind", { direction: "vertical" }, 600);
                                $(this).hide("blind", { direction: "vertical" }, 600);
                                return false;
                            });
                            $("select.charselect").change(function() {
                                if ($(this).val() == "customauthor"){
                                    $(".customauthorhide").show("blind", { direction: "vertical" }, 300);
                                }
                            });
                ';
                break;

            case "inspiration":
                //old code: resize to fit page
                /*$special = '$("img.inspiration").each(function() {
                                $(this).load(function(){
                                    var maxWidth = 480; // Max width for the image
                                    var ratio = 0;  // Used for aspect ratio
                                    var width = $(this).width();    // Current image width
                                    var height = $(this).height();  // Current image height

                                    // Check if the current width is larger than the max
                                    if(width > maxWidth){
                                        ratio = maxWidth / width;   // get ratio for scaling image
                                        $(this).css("width", maxWidth); // Set new width
                                        $(this).css("height", height * ratio);  // Scale height based on ratio
                                        height = height * ratio;    // Reset height to match scaled image
                                        width = width * ratio;    // Reset width to match scaled image
                                    }
                                });
                            });';/**/
                $special = '';
                break;

            default:
                $special = '';
        }
        break;
    case "characters":
        $special = '$("a.minimizer").live("click", function() {
                        $(this).siblings(".minimizable").hide("blind", { direction: "vertical" }, 600);
                        $(this).html("<img src=\"images/icons/zoom_in.png\" /> Maximize");
                        $(this).addClass("maximizer").removeClass("minimizer");
                        return false;
                    });
                    $("a.maximizer").live("click", function() {
                        $(this).siblings(".minimizable").show("blind", { direction: "vertical" }, 600);
                        $(this).html("<img src=\"images/icons/zoom_out.png\" /> Minimize");
                        $(this).addClass("minimizer").removeClass("maximizer");
                        return false;
                    });
                    $("a.minimizer").click();
                    $("input#name").focus();
        ';
        break;
    case "guilds":
        switch($_REQUEST["action"]){
			case 'create':
				$special = '$("#proceedguildcreatebutton").live("click", function() {
								var newguildname = $("#newguildname").val();
								var newguildowner = $("#newguildowner").val();
								$.post("ajax.php?subtopic=guilds&action=create", { "newguildname": newguildname, "newguildowner": newguildowner },
									function(data){
										if(data.success == 1){
											$("#guildcreateresponsebutton").remove();
											$("#guildcreateresponsetext").html(data.responsetext).css("color","inherit");
											$("#newcharname").val("");
										}else{
											$("#guildcreateresponsetext").html("<strong>Failure:</strong> " + data.errormsg).css("color","#cc0000");
										}
									},
								"json");
								$("#guildcreateformdiv").hide("blind", { direction: "vertical" }, 700);
								$("#guildcreateresponsediv").show("blind", { direction: "vertical" }, 700);
							});
							$("#guildcreateresponsebutton").live("click", function() {
								$("#guildcreateresponsediv").hide("blind", { direction: "vertical" }, 700);
								$("#guildcreateformdiv").show("blind", { direction: "vertical" }, 700);
							});';
				break;
				
			case 'edit':
				$special = 'var guildname = $("#guildname").text();
							$("#guildmetasavebutton").live("click", function() {
								var metadesc = $("#guildmetadescription").val();
								var metamotd = $("#guildmetamotd").val();
								$.post("ajax.php?subtopic=guilds&action=edit", { "guildname": guildname, "subaction": "meta", "metadesc" : metadesc, "metamotd" : metamotd },
									function(data){
										if(data.success == 1){
											$("#guildmetaresponse").html(data.responsetext).css("color","#00bb00").slideToggle(200, function(){
												setTimeout(function(){
													$("#guildmetaresponse").slideToggle(200);
												}, 2500);
											});
										}else{
											$("#guildmetaresponse").html("<strong>Failure:</strong> " + data.errormsg).css("color","#cc0000").slideToggle(200, function(){
												setTimeout(function(){
													$("#guildmetaresponse").slideToggle(200);
												}, 2500);
											});
										}
									},
								"json");
								return false;
							});
						';
				break;
		}
        break;
    case "creatures":
        $special = 'var searchVal = $("#searchname").val();
                    var refreshId = setInterval(function()
                    {
                        var newVal = $("#searchname").val();
                        if (newVal != searchVal){
                            $.post("ajax.php?subtopic=creatures&randval="+ Math.random(), { "searchname": newVal },
                                function(data){
                                    if(data.success == 1){
                                        alert("win");
                                    }else{
                                        alert("fail: "+data.errormsg);
                                    }
                                },
                            "json");
                            searchVal = newVal;
                        }
                    }, 500); //every 0.5 seconds';
        break;
    case "maps":
        $special = '$("img.resize").each(function() {
                        $(this).load(function(){
                            var maxWidth = 480; // Max width for the image
                            var ratio = 0;  // Used for aspect ratio
                            var width = $(this).width();    // Current image width
                            var height = $(this).height();  // Current image height

                            // Check if the current width is larger than the max
                            if(width > maxWidth){
                                ratio = maxWidth / width;   // get ratio for scaling image
                                $(this).css("width", maxWidth); // Set new width
                                $(this).css("height", height * ratio);  // Scale height based on ratio
                                height = height * ratio;    // Reset height to match scaled image
                                width = width * ratio;    // Reset width to match scaled image
                            }
                        });
                    });';
        break;
    default:
        $special = '';
}

$default = '
$(document).ready(function(){
    $("#navcontent").accordion({
        autoHeight: false,
        active: '.$navcat.',
        collapsible: true
    });

    //Form Submit stuff.
    $(".buttonsubmit").live("click", function() {
        $(this).parents("form").submit();
        return false;
    });
    $(".textinput.forminput").live("keyup", function(event) {
		if (event.which == 13) { //instead of keyCode or charCode we use .which because it contains both (i suppose you could call it jquery-exclusive)
            $(this).parents("form").submit();
            return false;
    	}
    });
    '.$special.'
});
';

$output = $default;
echo $output;

?>