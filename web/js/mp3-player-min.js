$(function(){function t(t){var e=t.attr("song"),i=t.attr("cover"),a=t.attr("album"),l=t.attr("artist"),r=t.text();n=new Audio("music/"+e),$("#title").text(r),$("#album").text(a),$("#artist").text(l),$("#cover-img").attr("style","background-image: url(images/covers/"+i+")"),$("#playlist li").removeClass("active"),t.addClass("active")}function e(){$(n).bind("timeupdate",function(){var t=$("#duration"),e=parseInt(n.currentTime%60),i=parseInt(n.currentTime/60)%60,a;e<10?t.html(i+":0"+e):t.html(i+":"+e),n.currentTime>0&&(a=Math.floor(100/n.duration*n.currentTime)),$("#progress").css("width",a+"%")})}function i(){var t=parseInt(n.duration);$(n).bind("timeupdate",function(){var e=parseInt(n.currentTime),i=t-e,a,l,r;a=i%60,l=Math.floor(i/60)%60,r=Math.floor(i/360)%60,a=a<10?"0"+a:a,l=l<10?"0"+l:l,r=r<10?"0"+r:r,$("#timeleft").html(r+":"+l+":"+a)})}function a(){n.addEventListener("loadeddata",function(){e(),i()})}var n;$("#pause-btn").hide(),t($("#playlist li:first-child")),$("#play-btn").click(function(t){t.preventDefault(),$("#play-btn").hide(),$("#pause-btn").show(),e(),i(),n.play()}),$("#pause-btn").click(function(t){t.preventDefault(),n.pause(),$("#play-btn").show(),$("#pause-btn").hide()}),$("#stop-btn").click(function(t){t.preventDefault(),n.pause(),$("#play-btn").show(),$("#pause-btn").hide(),n.currentTime=0,$("#progress").css("width","0%")}),$("#next-btn").click(function(e){e.preventDefault(),n.pause();var i=$("#playlist li.active").next();0==i.length&&(i=$("#playlist li:first-child")),t(i),a(),n.play()}),$("#prev-btn").click(function(e){e.preventDefault(),n.pause();var i=$("#playlist li.active").prev();0==i.length&&(i=$("#playlist li:last-child")),t(i),a(),n.play()}),$("#playlist li").click(function(e){n.pause(),t($(this)),$("#play-btn").hide(),$("#pause-btn").show(),a(),n.play()}),$("#volume-slider").change(function(){n.volume=parseFloat(this.value/10)}),$("#progress-bar").on("click",function(t){var e=$(this).width(),i=t.offsetX,a=i/e;$("#progress").css("width",Math.floor(100*a)+"%"),n.currentTime=n.duration*a})});