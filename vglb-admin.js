$ = jQuery;

var vglb = {
    id: 0,
    sid: 0,
    mode: 'static',
    max_ranks: 0,
    page: 'main'
};


$(document).ready(function() {

    vglb.page = $('#vglb_page_identifier').val();

    if(vglb.page === 'main'){
        vglb.sid = 1936;
        vglb.mode = 'current'
        vglb_get_leaderboard_preview(true);
        vglb_get_css_preview();

        if($('#vglb_color_scheme').val() !== 'Custom'){
            vglb_grey_inputs(true);
        }

        $('#vglb_color_scheme').change(function (){
            if($(this).val() !== 'Custom'){
                vglb_grey_inputs(true);
            }else{
                vglb_grey_inputs(false);
            }
        });

        $('#vglb_bg_color_header').keyup(function (){
            vglb_get_css_preview();
        });
        $('#vglb_font_color_header').keyup(function (){
            vglb_get_css_preview();
        });
        $('#vglb_bg_color1_body').keyup(function (){
            vglb_get_css_preview();
        });
        $('#vglb_bg_color2_body').keyup(function (){
            vglb_get_css_preview();
        });
        $('#vglb_font_color_body').keyup(function (){
            vglb_get_css_preview();
        });
        $('#vglb_hover_color_body').keyup(function (){
            vglb_get_css_preview();
        });
        $('#vglb_cell_padding').keyup(function (){
            vglb_get_css_preview();
        });

        $('#vglb_color_scheme').change(function (){
            vglb_get_css_preview();
        });

        $('.vglb_display_title').change(function (){
            if($(this).val() === "enabled"){
                $('.vglb_title_wrap_pre').show();
            }else{
                $('.vglb_title_wrap_pre').hide();
            }
        });

        $('.vglb_display_info').change(function (){
            if($(this).val() === "enabled"){
                $('.vglb_info_wrap_pre').show();
            }else{
                $('.vglb_info_wrap_pre').hide();
            }
        });

        return;
    }

    vglb.id = $('#vglb_promotion_id').val();
    vglb.sid = $('#vglb_promotion_id option:selected').attr('sid');

    vglb_get_short_code();
    vglb_get_leaderboard_preview();

    $('#vglb_promotion_id').change(function (){
        vglb.id = $(this).val();
        vglb.sid = $('option:selected', this).attr('sid');
        vglb_get_short_code();
        vglb_get_leaderboard_preview();
    });

    $('#vglb_max_ranks').keyup(vglb_delay(function (e) {
        if(vglb_check_max_ranks_val(this.value)){
            vglb.max_ranks = this.value.trim();
            vglb_get_short_code();
            vglb_get_leaderboard_preview();
        }
    }, 1000));

    $('.vglb_mode').change(function (){
        vglb.mode = $(this).val();
        vglb_get_short_code();
        vglb_get_leaderboard_preview();
    });
});

function vglb_grey_inputs(grey){

    $('.vglb_style_field').each(function (){

        $(this).removeClass('vglb_style_grey');

        if(grey){
            $(this).addClass('vglb_style_grey');
        }
    });
}

function vglb_get_short_code(){
    var short_code = '[vglb_leaderboard id="' + vglb.id + '"';

    if(vglb.max_ranks > 0){
        short_code += ' max_ranks="' + vglb.max_ranks + '"';
    }

    if(vglb.mode !== 'static'){
        short_code += ' sid="' + vglb.sid + '" lb="';

        if(vglb.mode === 'current'){
            short_code += 'current';
        }else{
            short_code += 'previous'
        }

        short_code += '"'
    }

    short_code += ']';

    $('#vglb_short_code').html(short_code);
}

function vglb_check_max_ranks_val(max_ranks){

    if($.isNumeric(max_ranks) && Math.floor(max_ranks) == max_ranks && max_ranks >= 0){
        $('#vglb_max_ranks_error').hide();
        return 1;
    }else{
        $('#vglb_max_ranks_error').html("Only integer values > 0 are legit");
        $('#vglb_max_ranks_error').show();
        return 0;
    }
}

function vglb_get_leaderboard_preview(replace_classes=false){

    $('#vglb_preview_wrap').html('loading...')

    var sid = 0;

    if(vglb.mode !== 'static'){
        sid = vglb.sid;
    }

    $.ajax( {
        url : ajaxurl,
        type : 'POST',
        data: {
            action: 'vglb_get_leaderboard_preview_wrap', promotion_id: vglb.id, max_ranks: vglb.max_ranks, sid: sid, lb: vglb.mode
        },
        success : function(response) {
            if(replace_classes) {
                var search = ['class="vglb_title_wrap"', 'class="vglb_info_wrap"', 'class="vglb_table"'];
                var replace = ['class="vglb_title_wrap_pre"', 'class="vglb_info_wrap_pre"', 'class="vglb_table_pre"'];
                response = vglb_replacer(response, search, replace);
            }
            $('#vglb_preview_wrap').html(response);
        }
    });
}


function vglb_delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

function vglb_get_css_preview(){

    var cell_padding, font_color_header, bg_color_header, font_color_body, bg_color1_body, bg_color2_body, hover_color_body = '';
    var scheme = $('#vglb_color_scheme').val();

    if(scheme === 'No Styles'){
        $('#vblb_preview_styles').html('');
        return;
    }

    if(scheme === 'Custom') {
        cell_padding = $('#vglb_cell_padding').val();
        font_color_header = $('#vglb_font_color_header').val();
        bg_color_header = $('#vglb_bg_color_header').val();
        font_color_body = $('#vglb_font_color_body').val();
        bg_color1_body = $('#vglb_bg_color1_body').val();
        bg_color2_body = $('#vglb_bg_color2_body').val();
        hover_color_body = $('#vglb_hover_color_body').val();

        var search = ['.vglb_title_wrap', '.glb_info_wrap', '.vglb_table'];
        var replace = ['.vglb_title_wrap_pre', '.glb_info_wrap_pre', '.vglb_table_pre'];
    }else{
        cell_padding = '5px';
        font_color_header = '#fff';
        bg_color_header = '#0099cc';
        font_color_body = '#333';
        bg_color1_body = '#f5f5f5';
        bg_color2_body = '#e5e5e5';
        hover_color_body = '#f3f0a6';

        if(scheme === 'Green'){
            bg_color_header = '#00cc00';
        }

        if(scheme === 'Red'){
            bg_color_header = '#cc0000';
        }
    }

    var css = 'table.vglb_table_pre {\n' +
        '            border-collapse: collapse;\n' +
        '            width: 100%;\n' +
        '        }\n' +
        '        table.vglb_table_pre td {\n' +
        '            padding: ' + cell_padding + ';\n' +
        '        }\n' +
        '        table.vglb_table_pre>thead>tr>th{\n' +
        '            background-color: ' + bg_color_header + ';\n' +
        '            color: ' + font_color_header + ';\n' +
        '            text-align: left;\n' +
        '            padding: ' + cell_padding + ';\n' +
        '        }\n' +
        '        table.vglb_table_pre>tbody {\n' +
        '            color: ' + font_color_body + ';\n' +
        '        }\n' +
        '        table.vglb_table_pre>tbody>tr:nth-child(odd) {\n' +
        '            background: ' + bg_color1_body + ';\n' +
        '        }\n' +
        '        table.vglb_table_pre>tbody>tr:nth-child(even) {\n' +
        '            background: ' + bg_color2_body +';\n' +
        '        }\n' +
        '        table.vglb_table_pre>tbody>tr:hover {\n' +
        '            background: ' + hover_color_body +';\n' +
        '        }';
    css += '\n';

    $('#vblb_preview_styles').html(css);
}


function vglb_replacer(str, search, replace){
    for(var i=0;i<replace.length;i++){
        str = str.replace(search[i], replace[i]);
    }
    return str;
}