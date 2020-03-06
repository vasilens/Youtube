function likeVideo(video_id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var res = JSON.parse(this.response);
            document.getElementById("likes-count").innerText = res.likes;
            document.getElementById("dislikes-count").innerText = res.dislikes;
            if (res.stat == 1) {
                document.getElementById("like").style.color = 'blue';
                document.getElementById("dislike").style.color = 'gray';
            }
            if (res.stat == -1){
                document.getElementById("like").style.color = 'gray';
                document.getElementById("dislike").style.color = 'gray';
            }
        }
    };
    xhttp.open("GET", "index.php?target=user&action=reactVideo&id=" + video_id + "&status=1", true);
    xhttp.send();
}

function dislikeVideo(video_id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var res = JSON.parse(this.response);
            document.getElementById("likes-count").innerText = res.likes;
            document.getElementById("dislikes-count").innerText = res.dislikes;
            if (res.stat == 0) {
                document.getElementById("like").style.color = 'gray';
                document.getElementById("dislike").style.color = 'blue';
            }
            if (res.stat == -1){
                document.getElementById("like").style.color = 'gray';
                document.getElementById("dislike").style.color = 'gray';
            }
        }
    };
    xhttp.open("GET", "index.php?target=user&action=reactVideo&id=" + video_id + "&status=0", true);
    xhttp.send();
}

function addComment() {
    var video_id = document.getElementById("video_id").value;
    var owner_id = document.getElementById("owner_id").value;
    var content = document.getElementById("content").value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var res = JSON.parse(this.response);
            var comment_id = res.id;
            document.getElementById("content").value = "";
            var table = document.getElementById("comments");
            var row = table.insertRow(0);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            row.id = "comment"+comment_id;
            cell1.innerHTML = "<img width='50px' src='" + res.avatar_url + "'>";
            cell2.innerHTML = res.name;
            cell3.innerHTML = res.date;
            cell4.innerHTML = res.content;
            cell5.innerHTML = "<img src='styles/images/likeImg.png' style='height: 30px;' id='like-comment"+comment_id+"' onclick='likeComment(" + comment_id +
                ")'></> (<span id='comment" + comment_id + "-likes'>" + res.likes +
                "</span>) <img src='styles/images/dislikeImg.png' style='height: 30px;' id='dislike-comment"+comment_id+"' onclick='dislikeComment(" + comment_id +
                ")'> (<span id='comment" + comment_id + "-dislikes'>" + res.dislikes +
                "</span>) <img style='height: 20px;margin-left: 5px;' src='styles/images/deleteIcon.png' onclick='deleteComment("+comment_id+")'>";
        }
    };
    xhttp.open("POST", "index.php?target=comment&action=add", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('video_id=' + video_id +  '&owner_id=' + owner_id + '&content=' + content);
}

function deleteComment(comment_id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("comment"+comment_id).remove();
        }
    };
    xhttp.open("GET", "index.php?target=comment&action=delete&id=" + comment_id, true);
    xhttp.send();
}

function likeComment(comment_id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var res = JSON.parse(this.response);
            document.getElementById("comment"+comment_id+"-likes").innerText = res.likes;
            document.getElementById("comment"+comment_id+"-dislikes").innerText = res.dislikes;
            if (res.stat == 1) {
                document.getElementById("like-comment"+comment_id).style.color = 'blue';
                document.getElementById("dislike-comment"+comment_id).style.color = 'gray';
            }
            if (res.stat == -1){
                document.getElementById("like-comment"+comment_id).style.color = 'gray';
                document.getElementById("dislike-comment"+comment_id).style.color = 'gray';
            }
        }
    };
    xhttp.open("GET", "index.php?target=comment&action=react&id=" + comment_id + "&status=1", true);
    xhttp.send();
}

function dislikeComment(comment_id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var res = JSON.parse(this.response);
            document.getElementById("comment"+comment_id+"-likes").innerText = res.likes;
            document.getElementById("comment"+comment_id+"-dislikes").innerText = res.dislikes;
            if (res.stat == 0) {
                document.getElementById("like-comment"+comment_id).style.color = 'gray';
                document.getElementById("dislike-comment"+comment_id).style.color = 'blue';
            }
            if (res.stat == -1){
                document.getElementById("like-comment"+comment_id).style.color = 'gray';
                document.getElementById("dislike-comment"+comment_id).style.color = 'gray';
            }
        }
    };
    xhttp.open("GET", "index.php?target=comment&action=react&id=" + comment_id + "&status=0", true);
    xhttp.send();
}

function followUser(user_id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("follow-button").innerText="Unsubscribe";
            document.getElementById("follow-button").onclick = function () {
                unfollowUser(user_id);
            };
        }
    };
    xhttp.open("GET", "index.php?target=user&action=follow&id=" + user_id, true);
    xhttp.send();
}

function unfollowUser(user_id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("follow-button").innerText="Subscribe";
            document.getElementById("follow-button").onclick = function () {
                followUser(user_id);
            };
        }
    };
    xhttp.open("GET", "index.php?target=user&action=unfollow&id=" + user_id, true);
    xhttp.send();
}

function showMyPlaylists(user_id, video_id){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if(this.readyState == 4 && this.status == 200){
            var playlists = JSON.parse(this.response);
            var text = document.getElementById("showPlaylists");
            text.style.display = "none";
            for (var i = 0; i < playlists.length; i++){
                var playlist = playlists[i];
                var table = document.getElementById("playlist-holder");
                var row = table.insertRow(i);
                var cell1 = row.insertCell(0);
                cell1.innerHTML = "<input type='checkbox' onclick='addToPlaylist("+playlist.id+ "," + video_id+")'>"
                    +playlist.playlist_title;
            }
            // var cell2 = row.insertCell(1);
            // cell2.innerHTML = "<button id='add"+playlist_id+""+video_id+"' onclick='addToPlaylist(" + playlist.id +
            //     video_id+")'>Save</button>"
        }
    };
    xhttp.open("GET", "index.php?target=playlist&action=getMyPlaylistsJSON&owner_id=" + user_id, true);
    xhttp.send();
}

function addToPlaylist(playlist_id, video_id){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var text = document.getElementById("showPlaylists");
            var newtext = document.createElement('p');
            newtext.innerHTML = "Added";
            text.parentNode.replaceChild(newtext, text);
        }
        };

    xhttp.open("GET", "index.php?target=playlist&action=addToPlaylist&playlist_id=" + playlist_id + "&video_id=" + video_id, true);
    xhttp.send();
}