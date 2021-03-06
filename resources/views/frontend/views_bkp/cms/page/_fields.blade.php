
<avored-form-input 
    field-name="name"
    label="{{ __('avored-framework::attribute.name') }}" 
    field-value="{!! $model->name ?? "" !!}" 
    error-text="{!! $errors->first('name') !!}"
    v-on:change="changeModelValue"
    autofocus="autofocus"
        >
</avored-form-input>



<avored-form-input 
    field-name="slug"
    label="{{ __('Slug') }}" 
    field-value="{!! $model->slug ?? "" !!}" 
    error-text="{!! $errors->first('slug') !!}"
    v-on:change="changeModelValue"
   
        >
</avored-form-input>
@php
    $content = (isset($model)) ? $model->getContent() : "";
@endphp
<div class="form-group">
    <label for="content">Content</label>
    <textarea id="content" name="content"
              class="summernote form-control"
    >{{ $content }}</textarea>
</div>

<avored-form-input 
    field-name="meta_title"
    label="{{ __('Meta Title') }}" 
    field-value="{!! $model->meta_title ?? "" !!}" 
    error-text="{!! $errors->first('meta_title') !!}"
    v-on:change="changeModelValue"
   
        >
</avored-form-input>

<avored-form-input 
    field-name="meta_description"
    label="{{ __('Meta Description') }}" 
    field-value="{!! $model->meta_description ?? "" !!}" 
    error-text="{!! $errors->first('meta_description') !!}"
    v-on:change="changeModelValue"
   
        >
</avored-form-input>


<div class="modal" id="widget-list-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Widget Selection</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('avored-framework::forms.select',
                            ['name' => 'widget_list',
                            'label' => 'Widget List',
                            'options' => $widgetOptions
                            ])
            </div>
            <div class="modal-footer">
                <button type="button" 
                        id="widget-insert-button" 
                        data-dismiss="modal" 
                        class="btn btn-primary">
                    Insert Widget
                </button>
                <button type="button"   
                    id="widget-close-button" 
                    data-dismiss="modal"
                     class="btn btn-default">
                     
                     Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')

    <script>

        (function (factory) {
            /* global define */
            if (typeof define === 'function' && define.amd) {
                // AMD. Register as an anonymous module.
                define(['jquery'], factory);
            } else if (typeof module === 'object' && module.exports) {
                // Node/CommonJS
                module.exports = factory(require('jquery'));
            } else {
                // Browser globals
                factory(window.jQuery);
            }
        }(function ($) {

            $.extend(true, $.summernote.lang, {
                'en-US': {
                    /* US English(Default Language) */
                    avoredWidget: {
                        exampleText: 'Widget Panel',
                        dialogTitle: 'Widget Panel',
                        okButton: 'OK'
                    }
                }
            });


            $.extend($.summernote.options, {
                avoredWidget: {
                    icon: '<i class="fab fa-usb"/>',
                    tooltip: 'Please Select AvoRed Widget'
                }
            });

            $.extend($.summernote.plugins, {
                'avoredWidget': function (context) {
                    var self = this,
                        ui = $.summernote.ui,
                        $note = context.layoutInfo.note,
                        $editor = context.layoutInfo.editor,
                        $editable = context.layoutInfo.editable,
                        $toolbar = context.layoutInfo.toolbar,

                        options = context.options,
                        lang = options.langInfo;

                    context.memo('button.avoredWidget', function () {


                        var button = ui.button({

                            contents: options.avoredWidget.icon,
                            tooltip: lang.avoredWidget.tooltip,
                            click: function (e) {
                               
                                context.invoke('avoredWidget.show');
                            }
                        });
                        
                        return button.render();
                    });


                    this.show = function () {
                        jQuery('#widget-list-modal').modal();
                        jQuery(document).on('click', '#widget-insert-button', function (event) {
                            var widgetIdentifier = jQuery('#widget_list').val();
                            context.invoke('editor.insertText', "### " + widgetIdentifier + " ###");
                            jQuery('#widget-list-modal').modal('hide');
                        });
                    };
                }
            });

        }));

        $(document).ready(function() {
            $('.summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol']],
                    ['misc', ['codeview']],
                    ['custom', ['avoredWidget', 'makeSpecialCharSetTable']],
                ]

            });
        });




    </script>


@endpush