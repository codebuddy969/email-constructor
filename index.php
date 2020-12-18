<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Constructor</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="index.css">
    <style>
        #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
        #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
        #sortable li span { position: absolute; margin-left: -1.3em; }
    </style>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    $( function() {
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();
    } );
    </script>
</head>

<script>

    /********************** Configs **********************/

    var store = {};

    var templates = [
        {
            name: 'core-configuration',
            template: `<?= file_get_contents('./core/configuration-block.php'); ?>`
        },
        {
            name: 'core-table',
            template: `<?= file_get_contents('./core/table-container.php'); ?>`
        },
        {
            name: 'header-image',
            template: `<?= file_get_contents('./templates/header-image.php'); ?>`
        },
        {
            name: 'simple-text',
            template: `<?= file_get_contents('./templates/simple-text.php'); ?>`
        },
        {
            name: 'spacing',
            template: `<?= file_get_contents('./templates/spacing.php'); ?>`
        },
        {
            name: 'heading',
            template: `<?= file_get_contents('./templates/heading.php'); ?>`
        },
        {
            name: 'button',
            template: `<?= file_get_contents('./templates/button.php'); ?>`
        },
        {
            name: 'double-image',
            template: `<?= file_get_contents('./templates/double-image.php'); ?>`
        },
        {
            name: 'footer-1',
            template: `<?= file_get_contents('./templates/footer-1.php'); ?>`
        },
        {
            name: 'ep-logo',
            template: `<?= file_get_contents('./templates/ep-logo.php'); ?>`
        },
        {
            name: 'bullet-row',
            template: `<?= file_get_contents('./templates/bullet-row.php'); ?>`
        }
    ];

    var options = [
        {
            name: 'header-image'
        },
        {
            name: 'simple-text'
        },
        {
            name: 'spacing'
        },
        {
            name: 'heading'
        },
        {
            name: 'button'
        },
        {
            name: 'double-image'
        },
        {
            name: 'footer-1'
        },
        {
            name: 'ep-logo'
        },
        {
            name: 'bullet-row'
        }
    ]

    /********************** Actions **********************/

    function initializeOptions() {

        /************************** Assign initial options to the sidebar **************************/

        $(options).each(function(index, data) {
            $('.sidebar').append('<button class="option-button" data-name="'+data.name+'">'+ data.name +'<span><img src="./previews/' + data.name +'.jpg"></span></button>');
        });

        /************************** Assign initial cookie based on domain name **************************/

        <?php if(file_exists("configs/{$_SERVER['HTTP_HOST']}.json")) { ?>
            store = JSON.parse(`<?= file_get_contents("./configs/{$_SERVER['HTTP_HOST']}.json"); ?>`);

            for (const [key, data] of Object.entries(store)) {
                generateCodeBlocks(key, data);
            }
        <?php } ?>

        $('#file-export').on('click', function() {
            helpers.exportTemplate("file");
        });

        $('#console-export').on('click', function() {
            helpers.exportTemplate("console");
        });

        $('#clear-cookie').on('click', function() {
            helpers.deleteConfiguration();
        });

        $('#save-template').on('click', function() {
            helpers.storeConfiguration();
        });

        $('body').on('click', '#duplicate-block', function() {
            duplicateCodeBlock();
        });
    }

    function generateCodeBlocks(key, data) {

        var identifier = key;
        var templateName = data.template;
        var optionsData = data.options;

        /************************** Assign Configuration Template to Variable **************************/

        var config = templates.filter(data => data.name === "core-configuration")[0];

        /************************** Assign View Template to Variable **************************/

        var view = templates.filter(data => data.name === templateName)[0];

        /************************** Exclude duplicates from options **************************/

        var options = $.parseHTML(config.template);
        var container = $(options).find('.options');

        /************************** Assign each options to rows options container **************************/
        
        var output = {};
        var pattern = '';
        var optionsLength = Object.keys(optionsData).length - 1;

        if (optionsLength > 0) {

        Object.entries(optionsData).forEach(([key, value], index) => {
            var template = '<div class="configs">'+
            '    <div class="configs__name">'+ key +'</div>'+
            '    <div class="configs__field">'+
            '        <input type="text" data-name="'+ key +'" value="'+ value +'" data-view="'+ templateName +'">'+
            '    </div>'+
            '</div>';

            $(container).append(template);

            var symbol = optionsLength != index ? '|' : '';

            pattern += '{{'+ key +'}}' + symbol;

            output['{{'+ key +'}}'] = value;
        });

        var regex = new RegExp(pattern, "gi");

        var template = view.template.replace(regex, function(matched){
            return output[matched];
        });
        
        } else {
            var template = view.template;
        }

        var code = $.parseHTML(template);

        $(code).attr('id', identifier);

        $('#sortable').append(code);

        $('#' + identifier).wrap( "<div class='row' />");
        $('#' + identifier).parent().append(options);

        addOptionEditingCapabilities();
    }

    function addCodeBlock() {
        $('body').on('click', 'button.option-button', function() {
            var templateName = $(this).data("name");

            /************************** Assign Configuration Template to Variable **************************/

            var config = templates.filter(data => data.name === "core-configuration")[0];

            /************************** Assign View Template to Variable **************************/

            var view = templates.filter(data => data.name === templateName)[0];

            /************************** Parse Template for options **************************/

            var optionsParsed = getFromBetween.get(view.template,"{{","}}");

            /************************** Exclude duplicates from options **************************/

            var optionsCleaned = [];

            $.each(optionsParsed, function(i, el){
                if($.inArray(el, optionsCleaned) === -1) optionsCleaned.push(el);
            });

            var identifier = helpers.generateUUID();
            var code = $.parseHTML(view.template);
            var options = $.parseHTML(config.template);
            var container = $(options).find('.options');

            /************************** Prepare global store values **************************/

            store[identifier] = {};
            store[identifier].template = view.name;
            store[identifier].options = {};

            /************************** Assign each options to rows options container **************************/

            $(optionsCleaned).each(function(index, data) {
                var template = '<div class="configs">'+
                '    <div class="configs__name">'+ data +'</div>'+
                '    <div class="configs__field">'+
                '        <input type="text" data-name="'+ data +'" data-view="'+ templateName +'">'+
                '    </div>'+
                '</div>';
                
                $(container).append(template);
            });

            $(code).attr('id', identifier);

            $('#sortable').append(code);

            $('#' + identifier).wrap( "<div class='row' />");
            $('#' + identifier).parent().append(options);

            addOptionEditingCapabilities();
        });
    }

    function addOptionEditingCapabilities() {
        $('body').find('[data-name]').on('change', function() {

            var templateName = $(this).data("view");
            var value = $(this).val();
            var name = $(this).data('name');
            var view = templates.filter(data => data.name === templateName)[0];

            var options = $(this).parents('.options');
            var row = $(this).parents('.row');
            var element = $(row).find('tr');
            var identifier = $(row).find('tr').attr('id');

            var output = {};
            var pattern = '';
            var childrenLength = $(options).children().length - 1;

            $(options).children().each(function(index, element) {
                var field = $(element).find('input');

                var fieldValue = $(field).val();
                var fieldData = $(field).data('name');

                store[identifier].template = templateName;
                store[identifier].options[fieldData] = fieldValue;

                var symbol = childrenLength != index ? '|' : '';
                
                pattern += '{{'+ fieldData +'}}' + symbol;

                output['{{'+ fieldData +'}}'] = fieldValue;
            });

            var regex = new RegExp(pattern, "gi");

            var template = view.template.replace(regex, function(matched){
                return output[matched];
            });

            var code = $.parseHTML(template);

            $(code).attr('id', identifier);

            $(element).replaceWith(code);

            helpers.setCookie(window.location.host, JSON.stringify(store), 365);
        });

        $('body').find('.configurator-remove').on('click', function() {
            var row = $(this).parents('.row');
            var identifier = $($(row).children('tr')[0]).attr('id');

            $(this).parents('.row').remove();

            delete store[identifier];

            helpers.setCookie(window.location.host, JSON.stringify(store), 365);
        });
    }

    /********************** Helpers **********************/

    var helpers = {
        exportTemplate: function(type) {
            var content = '';
            var container = $('#sortable');
            var view = templates.filter(data => data.name === "core-table")[0];

            $(container).children().each(function(index, element) {
                $(element).find('tr').removeAttr('id');
                content += $(element).find('tr').prop('outerHTML');
            });

            var re = /{{content}}/gi;

            var template = view.template.replace(re, content);

            switch (type) {
                case "file":
                    helpers.downloadFile("Template.txt", template);
                    break;
                case "console":
                default:
                    console.log(template);
                    break;
            }
        },
        generateUUID: function() {
            var s = [];
            var hexDigits = "0123456789abcdef";
            for (var i = 0; i < 36; i++) {
                s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
            }
            s[14] = "4";
            s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);
            s[8] = s[13] = s[18] = s[23] = "-";

            var uuid = s.join("");
            return uuid;
        },
        setCookie: function(name, value, days) {
            var d = new Date;
            d.setTime(d.getTime() + 24*60*60*1000*days);
            document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
        },
        getCookie: function(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        },
        deleteCookie: function(name) {
            setCookie(name, '', -1);
        },
        downloadFile: function(filename, text) {
            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);
        },
        storeConfiguration: function() {
            var config = {};
            $('#sortable').children().each(function(index, row) {
                var identified = $(row).find('tr').attr('id');
                config[index + "-" + identified] = store[identified];
            });

            $.ajax({
                type: "POST",
                url: "/request.php",
                data: {config: JSON.stringify(config), name: window.location.host},
                dataType:'JSON', 
                success: function(response){
                    alert(response);
                }
            });
        },
        deleteConfiguration: function() {
            $.ajax({
                type: "POST",
                url: "/request.php",
                data: {template_name: window.location.host},
                dataType:'JSON', 
                success: function(response){
                    alert(response);
                }
            });
        },
        duplicateCodeBlock: function() {
            alert('asdasd');
        }
    }

    var getFromBetween = {
        results:[],
        string:"",
        getFromBetween:function (sub1,sub2) {
            if(this.string.indexOf(sub1) < 0 || this.string.indexOf(sub2) < 0) return false;
            var SP = this.string.indexOf(sub1)+sub1.length;
            var string1 = this.string.substr(0,SP);
            var string2 = this.string.substr(SP);
            var TP = string1.length + string2.indexOf(sub2);
            return this.string.substring(SP,TP);
        },
        removeFromBetween:function (sub1,sub2) {
            if(this.string.indexOf(sub1) < 0 || this.string.indexOf(sub2) < 0) return false;
            var removal = sub1+this.getFromBetween(sub1,sub2)+sub2;
            this.string = this.string.replace(removal,"");
        },
        getAllResults:function (sub1,sub2) {
            if(this.string.indexOf(sub1) < 0 || this.string.indexOf(sub2) < 0) return;
            var result = this.getFromBetween(sub1,sub2);
            this.results.push(result);
            this.removeFromBetween(sub1,sub2);

            if(this.string.indexOf(sub1) > -1 && this.string.indexOf(sub2) > -1) {
                this.getAllResults(sub1,sub2);
            }
            else return;
        },
        get:function (string,sub1,sub2) {
            this.results = [];
            this.string = string;
            this.getAllResults(sub1,sub2);
            return this.results;
        }
    };


    /********************** Initializers **********************/

    $(function(){
        initializeOptions();
        addCodeBlock();
    });

</script>

<body>
    <div class="page">
        <div class="operations">
            <button id="file-export">File</button>
            <button id="console-export">Console</button>
            <button id="clear-cookie">Clear template</button>
            <button id="save-template">Save template</button>
        </div>
        <div class="sidebar"></div>
        <div class="main">
            <table class="content" style="margin: 0 auto; background-color: #ffffff;" align="center" border="0" cellpadding="0" cellspacing="0">
                <tbody id="sortable"></tbody>            
            </table>
        </div>
    </div>
</body>
</html>