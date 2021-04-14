

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


function deleteMedia(url){
    get(url).done(function(response){
        $('#modal_media_content').html(response);
        $('#modal_media_id').html("Supprimer une image");
        $('#modal_media').modal('show');
    });
}
function deleteTrick(url){
    get(url).done(function(response){
        $('#modal_media_content').html(response);
        $('#modal_media_id').html("Supprimer une figure");
        $('#modal_media').modal('show');
    });
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