
(function ($) {

    $.initFormCollection = function (collectionName) {

        var $collection = $('[data-collection="' + collectionName + '"]');

        $('[data-collection-add="' + collectionName + '"]').on('click', function (e) {

            var $this = $(this);
            var $padreContenedor = $this.parents('[data-collection-childs="' + collectionName + '"]');

            if ($padreContenedor.length > 0) {
                $collection = $('[data-collection="' + collectionName + '"]', $padreContenedor);
            }

            var prototype = $collection.attr('data-prototype');
            var $newForm = $(prototype.replace(new RegExp("__" + collectionName + "__", "g"), $collection.children().length));

            $(document).trigger('form-collection.' + collectionName + '.beforeAdd', $newForm);
            $collection.append($newForm);
            $(document).trigger('form-collection.' + collectionName + '.afterAdd', $newForm);
            // inicializo los autocomplete que estan en la colección agregada
            /*$('[data-autocomplete]', $collection).each(function(){
                $.initAutocomplete($(this).attr('id'));
            });*/

            // inicializo los collection que estan dentro de la colección
            /*$('[data-collection]', $newForm).each(function(){
                $.initFormCollection($(this).data('collection'));
            });*/
            $('input[type="file"]').on('change', function (e) {
                $('[for="' + e.target.id + '"]').html(e.target.files[0].name);
            });

            e.stopImmediatePropagation();
            e.preventDefault();
        });

        $(document).on('click', '[data-collection-del="' + collectionName + '"]', function (e) {
            var $this = $(this);
            var $contentToRemove = $this.parents($this.attr('data-collection-parent-to-remove'));
            $(document).trigger('form-collection.' + collectionName + '.beforeRemove', $contentToRemove);
            $contentToRemove.remove();
            $(document).trigger('form-collection.' + collectionName + '.afterRemove', $contentToRemove);
            e.preventDefault();
        });
    };

})(jQuery);
