raptor(function($) {
    if (!$('.raptor-editable-post').length) return;
    $('.raptor-editable-post').editor({
        uiOrder: [
            ['save', 'cancel'],
            ['dock'],
            ['showGuides'],
            ['viewSource'],
            ['undo', 'redo'],
            ['alignLeft', 'alignCenter', 'alignJustify', 'alignRight'],
            ['textBold', 'textItalic', 'textUnderline', 'textStrike'],
            ['textSub', 'textSuper'],
            ['listUnordered', 'listOrdered'],
            ['hr', 'quoteBlock'],
            ['fontSizeInc', 'fontSizeDec'],
            ['wordpressMediaLibrary'],
            ['link', 'unlink'],
            ['insertFile'],
            ['floatLeft', 'floatNone', 'floatRight'],
            ['tagMenu']
        ],
        enableUi: false,
        ui: {
            wordpressMediaLibrary: false,
            showGuides: false,
            dock: false,
            viewSource: false,
            textBold: true,
            textItalic: true,
            textUnderline: true,
            textStrike: true,
            textSub: true,
            textSuper: true,
            alignLeft: true,
            alignRight: true,
            alignCenter: true,
            alignJustify: true,
            quoteBlock: false,
            floatLeft: false,
            floatRight: false,
            floatNone: false,
            fontSizeInc: false,
            fontSizeDec: false,
            hr: true,
            undo: true,
            redo: true,
            link: true,
            unlink: true,
            listUnordered: true,
            listOrdered: true,
            tagMenu: false,
            save: true,
            cancel: true
        },
        plugins: {
            dock: {
                docked: true,
                dockUnder: '#wpadminbar'
            },
            saveJson: {
                showResponse: true,
                id: {
                    attr: 'data-post_id'
                },
                postName: raptorInPlace.action,
                ajax: {
                    url: raptorInPlace.url,
                    type: 'post',
                    cache: false,
                    success: function(){
                        if(document.getElementById('draft_mode_box'))document.getElementById('draft_mode_box').style.display = 'block';
                         elements = document.getElementsByClassName('raptor-editable-post');
                        for (var i = 0; i < elements.length; i++) {
                            elements[i].style.backgroundColor ="peachpuff";
                        }
                        if(document.getElementById("revision_list"))document.getElementById("revision_list").innerHTML = "<a href='#' onclick='location.reload();'>Click here to reload version list</a>";
                        if(document.getElementById("syt_all_sigs"))document.getElementById("syt_all_sigs").innerHTML = "";
                        if(document.getElementById("draftBanner"))document.getElementById("draftBanner").innerHTML = '<b>This policy is in draft mode, it will not be live until it has been approved.</b>';
                        
                        //document.getElementById('raptor-editable-post').style.backgroundColor  = 'peachpuff';
                    },
                    data: function(id, contentData) {
                        var data = {
                            action: raptorInPlace.action,
                            posts: contentData,
                            nonce: raptorInPlace.nonce
                        };
                        return data;
                    }
                }
            },
            imageResize: {
                allowOversizeImages: raptorInPlace.allowOversizeImages
            }
        }
    });
});
