

function changeMediaUne(url){

    get(url).done(function(response){
        $('#modal_media_content').html(response);
        $('#modal_media_id').html("Changer l'image à la une");

        $('#modal_media').modal('show');

    });

}

function addMedia(trick){

}

function editMedia(media){

}

function deleteMedia(media){

}

function get(url){
// local var

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