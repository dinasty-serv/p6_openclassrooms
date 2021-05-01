

function changeMediaUne(url){
    get(url).done(function(response){
        $('#modal_media_content').html(response);
        $('#modal_media_id').html("Changer l'image à la une");
        $('#modal_media').modal('show');
    });

}

function addMediaTrick(url){
    get(url).done(function(response){
        $('#modal_media_content').html(response);
        $('#modal_media_id').html("Ajouter un media");
        $('#modal_media').modal('show');
    });
}

function addVideoTrick(url){
    get(url).done(function(response){
        $('#modal_media_content').html(response);
        $('#modal_media_id').html("Ajouter une vidéo");
        $('#modal_media').modal('show');
    });
}

function editMedia(media){

}

function deleteMedia(media){

}

function get(url){
    return $.ajax({
        type: 'GET',
        url: url,
        dataType: "html",

        success: function (data) {
        },
        errors: function (error){
            alert(error);
        }
    });
}