<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title></title>
        <style>
            .form_row{height: 40px;}
            .form_label{display: inline-block;width: 250px;text-align: right;}
            .form_field{display: inline;}
            .div_service{margin:3% 0;}
            .list_1 li{padding: 10px 0;}
            .list_1 li a{color: #1383e4;font-weight: bold;font-family: verdana;}
        </style>
    </head>
    <body>
        <div id="top">&nbsp;</div>
        <div style="position:fixed;right: 5%;top:40%;">
            <a href="#top"><input type="button" value="Top"/></a>
        </div>
        <div style="width:35%;text-align: left;float: left;">
            <h3>Paygate API in PHP</h3>
            <ol type="1" class="list_1">
                <li><a style="background : yellow" href="#add_card_service">Add card</a> (addCard)</li>
                <li><a style="background : yellow" href="#remove_card_service">Remove card</a> (removeCard)</li>           
                <li><a style="background : yellow" href="#chargeCard">charge card</a> (chargeCard)</li>
            </ol>
        </div>
        <div style="clear:both;"></div>


        <div id="add_card_service" class="div_service">
            <form action="services.php?/addCard" method="post">
                <h3>Add a credit card</h3>                
                <div class="form_row">
                    <div class="form_label">card token : </div>
                    <div class="form_field"><input type="text" name="ent_token" />name="ent_token",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_label">ent_cvc : </div>
                    <div class="form_field"><input type="text" name="ent_cvc" />name="ent_cvc",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_field"><input type="submit" name="ent_submit" value="Submit" /></div>
                </div>
            </form>
        </div>

        <div id="remove_card_service" class="div_service">
            <form action="services.php?/removeCard" method="post">
                <h3>Remove a credit card</h3>                
                <div class="form_row">
                    <div class="form_label">card token : </div>
                    <div class="form_field"><input type="text" name="ent_token" />name="ent_token",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_field"><input type="submit" name="ent_submit" value="Submit" /></div>
                </div>
            </form>
        </div>

        <div id="chargeCard" class="div_service">
            <form action="services.php?/chargeCard" method="post">
                <h3>charge card</h3>                
                <div class="form_row">
                    <div class="form_label">card token : </div>
                    <div class="form_field"><input type="text" name="ent_token" />name="ent_token",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_label">ent_cvc : </div>
                    <div class="form_field"><input type="text" name="ent_cvc" />name="ent_cvc",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_label">amount : </div>
                    <div class="form_field"><input type="text" name="amount" />name="amount",type="number"</div>
                </div>
                <div class="form_row">
                    <div class="form_label">customer FirstName : </div>
                    <div class="form_field"><input type="text" name="customerFirstName" />name="customerFirstName",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_label">customer LasttName : </div>
                    <div class="form_field"><input type="text" name="customerLastName" />name="customerLastName",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_label">customer Phone : </div>
                    <div class="form_field"><input type="text" name="customerPhone" />name="customerPhone",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_label">customer Email : </div>
                    <div class="form_field"><input type="text" name="customerEmail" />name="customerEmail",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_label">currency Symbol (ZAR) : </div>
                    <div class="form_field"><input type="text" name="currencySymbol" />name="currencySymbol",type="string"</div>
                </div>
                <div class="form_row">
                    <div class="form_field"><input type="submit" name="ent_submit" value="Submit" /></div>
                </div>
            </form>
        </div>

    </body>
</html>