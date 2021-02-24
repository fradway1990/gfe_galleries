(function($){
  var mediaLibrary = window.wp.media({
    // Accepts [ 'select', 'post', 'image', 'audio', 'video' ]
    frame: 'select',
    title: 'Select Images',
    multiple: true,
    library: {
        order: 'DESC',
        orderby: 'date',
        type: 'image',
        search: null,
        uploadedTo: null
    },
    button: {
        text: 'Done'
    }
  });

  $('body').on('click','.gallery-thumb-holder.add-image',function(e){
    e.preventDefault();
    mediaLibrary.open();
  });

  function assignOrder(){
    var orderInputs = $('.gfe-order');
    for(var i = 0; i < orderInputs.length;i++){
      orderInputs.eq(i).attr('name','gfe_gallery['+i+']');
    }
  }
  mediaLibrary.on( 'select', function() {
    var selectedImages = mediaLibrary.state().get( 'selection' ).toJSON();
    console.log(selectedImages);
    var imageThumbs = '';
    for(var i = 0; i < selectedImages.length;i++){
        var url = selectedImages[i].url;
        var imageID = selectedImages[i].id;
        var imageThumb = `
        <div class='gallery-thumb-holder gfe-image' style='background-image:url(${url})'>
          <div class='gfe-delete-image'>&#x2716;</div>
          <input value='${imageID}' type='hidden' class='gfe-order' name=''>
        </div>
        `;
        imageThumbs+=imageThumb;
    }
    $(imageThumbs).insertBefore('.gallery-thumb-holder.add-image');
    assignOrder();
  });

  $('body').on('click','.gfe-delete-image',function(e){
    $(this).parent().remove();
    assignOrder();
  });

  $('.gallery-images-holder').sortable({
    items:'.gfe-image',
    update:function(event,ui){
      assignOrder();
    }
  });
}(jQuery));
