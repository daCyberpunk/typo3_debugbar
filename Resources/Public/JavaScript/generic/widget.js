if (typeof(PhpDebugBar) === 'undefined') {
    // namespace
    var PhpDebugBar = {};
    PhpDebugBar.$ = jQuery;
}

(function($) {

    /*typo3_debugbar/Resources/Public/JavaScript/generic/widget.js*/
    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying sql queries
     *
     * Options:
     *  - data
     */
    var TYPO3GenericWidget = PhpDebugBar.Widgets.TYPO3GenericWidget = PhpDebugBar.Widget.extend({

        className: csscls('typo3-debugbar-generic'),
        render: function() {
            var self = this;

            this.$list = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, value) {
                    if (value.message_html) {
                        var val = $('<span />').addClass(csscls('value')).html(value.message_html).appendTo(li);
                    } else {
                        try {
                            var m = JSON.stringify( JSON.parse( value.message ), undefined, 2 ),
                                isJson = true;
                        } catch ( exception ) {
                            var m = value.message;
                            if (m.length > 100) {
                                m = m.substr(0, 100) + "...";
                            }
                        }

                        var val = $('<span />').addClass(csscls('value')).text(m).appendTo(li);
                        if (!value.is_string || value.message.length > 100) {
                            var prettyVal = value.message;
                            if (!value.is_string) {
                                prettyVal = null;
                            }
                            li.css('cursor', 'pointer').click(function () {
                                if (val.hasClass(csscls('pretty'))) {
                                    val.text(m).removeClass(csscls('pretty'));
                                } else {
                                    if ( !isJson ) {
                                        prettyVal = prettyVal || PhpDebugBar.Widgets.createCodeBlock(value.message, 'php');
                                        val.addClass(csscls('pretty')).empty().append(prettyVal);
                                    } else {
                                        prettyVal = prettyVal || PhpDebugBar.Widgets.createCodeBlock(m, 'json');
                                        val.addClass(csscls('pretty')).empty().append(prettyVal);
                                    }
                                }
                            });
                        }
                    }

                    if (value.collector) {
                        $('<span />').addClass(csscls('collector')).text(value.collector).prependTo(li);
                    }
                    if (value.label) {
                        val.addClass(csscls(value.label));
                        $('<span />').addClass(csscls('label')).text(value.label).prependTo(li);
                    }
                }});

            this.$list.$el.appendTo(this.$el);
            this.$toolbar = $('<div><i class="phpdebugbar-fa phpdebugbar-fa-search"></i></div>').addClass(csscls('toolbar')).appendTo(this.$el);

            $('<input type="text" />')
                .on('change', function() { self.set('search', this.value); })
                .appendTo(this.$toolbar);

            this.bindAttr('data', function(data) {
                this.set({ exclude: [], search: '' });
                this.$toolbar.find(csscls('.filter')).remove();

                var filters = [], self = this;
                for (var i = 0; i < data.length; i++) {
                    if (!data[i].label || $.inArray(data[i].label, filters) > -1) {
                        continue;
                    }
                    filters.push(data[i].label);
                    $('<a />')
                        .addClass(csscls('filter'))
                        .text(data[i].label)
                        .attr('rel', data[i].label)
                        .on('click', function() { self.onFilterClick(this); })
                        .appendTo(this.$toolbar);
                }
            });

            this.bindAttr(['exclude', 'search'], function() {
                var data = this.get('data'),
                    exclude = this.get('exclude'),
                    search = this.get('search'),
                    caseless = false,
                    fdata = [];

                if (search && search === search.toLowerCase()) {
                    caseless = true;
                }

                for (var i = 0; i < data.length; i++) {
                    var message = caseless ? data[i].message.toLowerCase() : data[i].message;

                    if ((!data[i].label || $.inArray(data[i].label, exclude) === -1) && (!search || message.indexOf(search) > -1)) {
                        fdata.push(data[i]);
                    }
                }

                this.$list.set('data', fdata);
            });
        },

        onFilterClick: function(el) {
            $(el).toggleClass(csscls('excluded'));

            var excludedLabels = [];
            this.$toolbar.find(csscls('.filter') + csscls('.excluded')).each(function() {
                excludedLabels.push(this.rel);
            });

            this.set('exclude', excludedLabels);
        }


//         render: function() {
//             var self = this;
//             this.constructor.__super__.render.apply(self)
// //             console.log(self,':24')
//             this.bindAttr('data', function(data) {
// // console.log(self,':27')
//                 console.log(this,':24')
//                 data[1]['message_html'] = syntaxHighlight( JSON.stringify(data[1]['message']), undefined, 4 );
//                 data[ 1 ][ 'message' ] = null;
//                 // data[ 1 ][ 'is_string' ] = true;
//                 this.$list.set('data', data );
//             });
//             function syntaxHighlight(json) {
//                 json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
//                 return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
//                     var cls = 'number';
//                     if (/^"/.test(match)) {
//                         if (/:$/.test(match)) {
//                             cls = 'key';
//                         } else {
//                             cls = 'string';
//                         }
//                     } else if (/true|false/.test(match)) {
//                         cls = 'boolean';
//                     } else if (/null/.test(match)) {
//                         cls = 'null';
//                     }
//                     return '<span class="' + cls + '">' + match + '</span>';
//                 });
//             }
//
//
//
//         }
    });


    /**
     * An extension of KVListWidget where the data represents a list
     * of variables
     *
     * Options:
     *  - data
     */
    var SessionWidget = PhpDebugBar.Widgets.SessionWidget = PhpDebugBar.Widgets.KVListWidget.extend({

        className: csscls('kvlist varlist'),

        itemRenderer: function(dt, dd, key, value) {
            $('<span />').attr('title', key).text(key).appendTo(dt);

            if ( typeof value === 'object' ) {
                this.$list = new PhpDebugBar.Widgets.SessionWidget();
                this.$list.set( 'data', value );
                value = this.$list.$el;
                // console.log(value,':180')
                // console.log(value[0],':181')
                // console.log($('<div \>').append(value).html(),':182')
                var v = value;
                dd.append(value[0])
                // this.$list.$el.appendTo(dd);
                // console.log(this.$list,':179')
                var clickHandler = function(){
                    if (dd.hasClass(csscls('pretty'))) {
                        dd.append( value );
                        dd.removeClass(csscls('pretty'));
                    } else {
                        dd.append( value );
                        dd.addClass(csscls('pretty'));
                        // console.log(value,':189')
                        // console.log(value[0],':190')
                        // console.log($('<div \>').append(value).html(),':191')
                        // prettyVal = prettyVal || PhpDebugBar.Widgets.createCodeBlock(value);
                        // console.log(prettyVal,':193')
                        // dd.addClass(csscls('pretty')).empty().append(prettyVal);
                    }
                }
            } else {
                var v = value;
                if (v && v.length > 100) {
                    v = v.substr(0, 100) + "...";
                }
                dd.text( v );
                var clickHandler = function(){
                    if (dd.hasClass(csscls('pretty'))) {
                        dd.text(v).removeClass(csscls('pretty'));
                    } else {
                        prettyVal = prettyVal || PhpDebugBar.Widgets.createCodeBlock(value);
                        dd.addClass(csscls('pretty')).empty().append(prettyVal);
                    }
                }
            }
            var prettyVal = null;
            dd.click(clickHandler);
        }

    });

})(PhpDebugBar.$);
