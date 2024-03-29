/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var onCartUpdate = new Array();

function updateCart(url, action){
    
    $.post(url,{actionAjax : 'updateCart'},
        function(data){
            var qty    = data.Quantity;
            var subtot = data.Subtotal;

            if(qty == '' || qty == null)
                qty = '0';
            if(subtot == '' || subtot == null)
                subtot = '0';

            if( qty != 'null')
            {
//                var test = $('#cart-item-count').attr('id');
                if (parseInt(qty) > 1)
                    $('#cart-item-count').text(qty + ' items');
                else
                    $('#cart-item-count').text(qty+ ' item');

            }

            if( subtot != null)
                $('p#subtotal span').text(subtot + '$');

            if(action == 'updated' && subtot != null)
                $('#subTotalValue span').text(subtot);

        },
        'json'
    );
}

function addToCart(url, itemId, lang){
    $.post(url,{actionAjax : 'addToCart', itemID : itemId, langId: lang},
        function(data){
            updateCart(url);
        }
    );
}

var cartActions = {
    response: null,
    optionsList: null,
    ajax: function ()
    {
        var url = defaultProperties.baseUrl + '/cart/index/cartdetails/do/';
        url += defaultProperties.setUrlParams();

        $.post(
            url,
            null,
            function(data)
            {
                if (data == 'deletedRow')
                    cartActions.removeLine();
                if (data == 'deleted')
                    updateCart(defaultProperties.baseUrl + '/cart/index/ajax');
                if (data == 'updated')
                    cartActions.updateLine();

                var tmpArray = data.split('-');
                if (tmpArray[0] == 'inserted')
                    cartActions.newLine(tmpArray[1]);
            },
            'json'
        );
    },
    
    disableItem: function()
    {
        var divParent = defaultProperties.currentElem.parents('div:first');
        var nextDiv = divParent.nextAll('div') ;

            nextDiv.slice(0,2).slideToggle();
    },
    newLine: function(lastId)
    {
        defaultProperties.cartItemsId = lastId;
        
        var parent    = defaultProperties.currentElem.parent();
        var prevLine  = $(parent).prev();
        prevLine.children('.deleteLine').show();
        // duplicate the previous row
        var addedLine = prevLine.clone();
        // Create the new id with the new index from db
        var newId  = defaultProperties.idsData[0] + '-';
        newId     += defaultProperties.idsData[1] + '-';
        newId     += defaultProperties.idsData[2] + '-';
        newId     += defaultProperties.cartItemsId;

        // Set the new id
        addedLine.attr('id', newId);
        // write the new section
        prevLine.after(addedLine);
        //For each element in the duplicated div, find children and set the new id
        addedLine.children().each(function(){
            var idValue = $(this).attr('class') + '-' + defaultProperties.idsData[1] + '-' + defaultProperties.idsData[2] + '-' + defaultProperties.cartItemsId;
            $(this).attr('id', idValue );

            //For each children element, find children and set the new id qnd reset values
            $(this).children().each(function(){
                var idChildren = $(this).attr('class') + '-' + defaultProperties.idsData[1] + '-' + defaultProperties.idsData[2] + '-' + defaultProperties.cartItemsId;
                $(this).attr('id', idChildren );
                
                if (this.type == 'text')
                    $(this).val(1);
                    
                if (this.type == 'select-one')
                    $(this).children().removeAttr('selected');
            });
        });

    },
    removeLine: function()
    {
        var container = defaultProperties.currentElem.parents('div.product:first');
        container.remove();
        cartActions.calculate();
    },
    filterSelect: function(select, langId, upload)
    {
        if(select.val() == '')
        {
            select.next().hide();
        }
        else
        {
            select.next().show();
            var selectedS   = select.prev().val();
            var selectedOpt = select.children(':selected');
            var catId       = selectedOpt.val();
            var nextSelect  = select.next();
            defaultProperties.action = 'getSizes';
            defaultProperties.category = catId;

            var url = defaultProperties.baseUrl + '/cart/index/cartdetails/do/';
            url += defaultProperties.setUrlParams();

            $.getJSON(
                url,
                {langId: langId},
                function(data)
                {
                    var options = '<option value="">Choisir</option>';

                    jQuery.each(data, function(id, size) {
                        var selectedTxt = '';
                        if (id == selectedS)
                            selectedTxt = 'selected="selected"';
                        options += '<option value="'+id+'" ' +selectedTxt+' >'+size+'</option>' ;
                    });

                    nextSelect.html(options);
                    return false;
                }
            );
        }
    },
    total: function(element, type){
        var total   = 0;
        var iterate = $('div[id^=' + element + ']');
        
        if(type == 'class')
            iterate = $('.' + element + ' span');
        
        $(iterate).each(function(){
            total = total + parseFloat($(this).text());
        });

        total = Math.round(total*100)/100;

        return total.toFixed(2);
    },
    updateLine: function()
    {
        var url = defaultProperties.baseUrl + '/cart/index/ajax/actionAjax/';
        url += defaultProperties.setUrlParams();

        $.post(url,{},
            function(data)
            {
                var parent   = defaultProperties.currentElem.parent();
                var nextDiv  = $(parent).nextAll('.sumLine');
                var sumField = nextDiv.children('span');

                if(data == '')
                    data = '0';

                if( data != null)
                {
                    var subTot   = parseFloat(data).toFixed(2);
                    $(sumField).text(subTot);
                    cartActions.calculate();
                }
            },
            'json'

        );
    },
    calculate: function()
    {
        $('#subTotalValue span').text(cartActions.total('sumLine'));
        var subTot = parseFloat($('#subTotalValue span').text());
        
        if (subTot > defaultProperties.limitShip)
        {
            $('#transportValue span').text(0);
            $('p.infoTpsFees').hide();
        }
        else
        {
            $('#transportValue span').text(defaultProperties.shipFee);
            $('p.infoTpsFees').show();
        }
        
        if (subTot > defaultProperties.limitOrder)
        {
            $('li.nextStep').show();
            $('p.infoLimitOrder').hide();
        }    
        else
        {
            $('li.nextStep').hide();
            $('p.infoLimitOrder').show();
        }
        
        var userProv = parseInt($('#userTaxProv').val());
        var userFed  = parseInt($('#userTaxFed').val());
        var transport = parseFloat($('#transportValue span').text());
        var subTps = 0;
        var subTvq = 0;
        var value = 0;
        // Test if the user is exempt from taxes
        //   0 = false, taxes habe to be calculated
        //   1 = true, the customer is exempt from taxes, no need to calculate.
        if(!userFed)
        {
            // for each item line, test if the item has tax else not added.
            $('.taxFed').each(function(){
                
                if(parseInt($(this).val()))
                    subTps += parseFloat($(this).parent().nextAll('div[id^=sumLine]').children('span').text());
            });
            value = subTps + transport;
            $('#tpsValue span').text(defaultProperties.getTpsValue(value));
        }
        
        if(!userProv)
        {
            $('.taxProv').each(function(){
                tesxt = $(this).parent().next().children('span');
                if(parseInt($(this).val()))
                    subTvq += parseFloat($(this).parent().nextAll('div[id^=sumLine]').children('span').text());
            });
            value = subTvq + transport;
            $('#tvqValue span').text(defaultProperties.getTvqValue(value));
        }

        $('.unitPrice').each(function(){
            var subTot = parseFloat($(this).parent().next().children('span').text());
            var tmpQty = parseFloat($(this).parent().prev().children('input').val());

            var unitPrice = subTot / tmpQty;
            $(this).text(unitPrice.toFixed(2));
        });
        if(defaultProperties.nbPoint)
        {
            var totBonus = subTot * defaultProperties.nbPoint;
            $('#totBonus').text(Math.round(totBonus));
        }
        
        $('#totalValue').text(cartActions.total('number', 'class') + ' $');


    }

}

var defaultProperties = {
    baseUrl: null,
    currentElem: null,
    pId: null,
    itemId: null,
    idsData: null,
    qty: null,
    category: null,
    size: null,
    action:null,
    disabled:null,
    cartItemsId: null,
    lastId: null,
    tvq: 0,
    tps: 0,
    nbPoint: 0,
    shipFee: 0,
    limitShip: 0,
    limitOrder: 25,
    format: true,
    init: function(
        baseUrl,
        currentElem,
        idsData,
        action,
        category,
        size)
    {
        if (this.baseUrl != undefined)
            this.baseUrl     = baseUrl;
        
        this.currentElem = currentElem;
        this.idsData     = idsData;
        this.pId         = idsData[1];
        this.itemId      = idsData[2];
        this.cartItemsId = idsData[3];
        this.category    = category;
        this.size        = size;
        this.qty         = currentElem.val();
        this.action      = action;
    },
    setUrlParams: function()
    {
        var url = '';
        var tmp = '';

        if (this.action)
            url += this.action;

        if (this.pId)
            url += '/pId/' + this.pId;

        if (this.itemId)
            url += '/itemId/' + this.itemId;

        if (this.cartItemsId)
            url += '/cartItemsId/' + this.cartItemsId;
        
        if (this.qty)
            tmp = '/quantity/' + this.qty ;

        if (this.category)
            tmp = '/category/' + this.category;

        if (this.size)
            tmp = '/size/' + this.size;

        if (this.action == 'disable')
        {
            this.disabled = 0;

            if (this.currentElem.is(':checked'))
                this.disabled = 1;

            tmp = '/disable/' + this.disabled;
        }

        url += tmp;
        
        return url;
    },
    setBaseUrl: function(url)
    {
         defaultProperties.baseUrl = url;
    },
    setCategory: function(category)
    {
         defaultProperties.category = category;
    },
    setTps: function(tax)
    {
        defaultProperties.tps = tax;
    },
    setTvq: function(tax)
    {
        defaultProperties.tvq = tax;
    },
    setShipFee: function(val)
    {
        defaultProperties.shipFee = val;
    },
    setLimitShip: function(val)
    {
        defaultProperties.limitShip = val;
    },
    setLimitOrder: function(val)
    {
        defaultProperties.limitOrder = val;
    },
    setNbPoint: function(val)
    {
        defaultProperties.nbPoint = val;
    },
    getTvqValue: function(sum)
    {
        var tvq     = this.tvq/100;
        this.format = false;
        var val     = (sum + this.getTpsValue(sum)) * tvq;
        
        val = Math.round(val*100)/100
        val = val.toFixed(2)

        return val;
    },
    getTpsValue: function(sum)
    {
        var tps = this.tps / 100;
        var val = Math.round(sum * tps * 100)/100;
        if(this.format)
            val = val.toFixed(2);
        
        return val;
    }
}

var modalWindow = {

    display: function(element, langId)
    {
        if  (element == undefined)
            element = $(this);
        
        var popID = element.attr('rel'); //Get Popup Name
        var popURL = element.attr('href'); //Get Popup href to define size

        //Pull Query & Variables from href URL
        var query= popURL.split('?');
        var dim= query[1].split('&');
        var popWidth = dim[0].split('=')[1]; //Gets the first query string value

        var param = 0;
        var catId = defaultProperties.category;
        if (dim.length > 1)
        {
            param = dim[1].split('=')[1];
            catId = 0;
        }

        // Get the text and add it to the body
        if (catId != undefined)
        {
            $.getJSON(
                defaultProperties.baseUrl + '/catalog/index/category-texts/',
                {
                    categoryId: catId,
                    typeText: popID,
                    langId : langId,
                    param : param
                },
                function(data){
                    var title = data.TITLE;
                    var text  = data.TEXT;

                    var content = '<div id="' + popID + '" class="popup_block">';
                    content += '<h1>' + title + '</h1>';
                    content += text;
                    content += '</div>';

                    $('body').append(content);
                    //Fade in the Popup and add close button
                    $('#' + popID).fadeIn().css({'width': Number( popWidth )}).prepend('<a href="#" class="close"><img src="close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');

                    //Define margin for center alignment (vertical   horizontal)
                    var popMargTop = ($(window).height()) / 2;
                    var popMargLeft = ($('#' + popID).width() - 10) / 2;

                    //Apply Margin to Popup
                    $('#' + popID).css({
                        'margin-top' : -popMargTop,
                        'margin-left' : -popMargLeft,
                        'height' : $(window).height() * 0.93
                    });
                    //Fade in Background
                    //Add the fade layer to bottom of the body tag.
                    $('body').append('<div id="fade"></div>');
                    //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies
                    $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

                    return false;
                }
            )
        }
        var relValue = element.attr('rel');
        if (relValue == 'hiddenStaticText')
        {
            var content = $('#' + popID).html();
            $('#' + popID).remove();
            $('body').append('<div id="' + popID + '" class="popup_block">' + content + '</div>');
            //Fade in the Popup and add close button
            $('#' + popID).fadeIn().css({'width': Number( popWidth )}).prepend('<a href="#" class="close"><img src="close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');

            //Define margin for center alignment (vertical   horizontal)
            var popMargTop = ($(window).height()) / 2;
            var popMargLeft = ($('#' + popID).width() - 10) / 2;

            //Apply Margin to Popup
            $('#' + popID).css({
                'margin-top' : -popMargTop,
                'margin-left' : -popMargLeft,
                'display' : 'block',
                'height' : $(window).height() * 0.93
            });

            //Fade in Background
            $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
            $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies

            return false;
        }
    },

    close: function()
    {
        //Close Popups and Fade Layer
        $('#fade , .popup_block').fadeOut(function() {
            $('#fade, a.close').remove();  //fade them both out
            return false;
        });
    }
}