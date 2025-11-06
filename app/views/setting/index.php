<?php require APPROOT . 'views/inc/header.php'; ?>

<div class="container-ms">
    <div class="w3-text-white w3-center">
        <table class="no-border">
            <tr id="header">
                <td width="100%"><h3><?php echo $text['setting'];?></h3></td>
                <td><img src="./img/btn_home.png" style="margin-right: 10px" onclick="back()"></td>
            </tr>
        </table>
    </div>

    <div class="main-content">
        <div class="center-content">
            <div class="w3-center ">
                <button id="bnt1" name="Controller_Display" class="button active" onclick="OpenButton('Controller')"><?php echo $text['controller_setting']; ?></button>
                <button id="bnt2" name="System_Display" class="button" onclick="OpenButton('System')"><?php echo $text['system_setting']; ?></button>
                <button id="bnt3" name="Barcode_Display" class="button" onclick="OpenButton('Barcode')"><?php echo $text['system_barcode_setting']; ?></button>
                <button id="bnt4" name="Connect_Display" class="button" onclick="OpenButton('Connect')" <?php if($_SESSION['privilege'] != 'admin'){echo "style=\"display:none\" ";} ?>><?php echo $text['system_connect_setting']; ?></button>
                <button id="bnt5" name="iDas_Display" class="button" onclick="OpenButton('Update')">iDAS</button>
            </div>

             <!-- controller_setting OP -->
                <?php require_once '../app/views/setting/controller_setting.php';?>
            <!-- controller_setting ED -->

            <!-- System_Setting OP -->
                <?php require_once '../app/views/setting/system_setting.php';?>
            <!-- System_Setting ED -->
               

            <!-- barcode_setting OP -->
                <?php require_once '../app/views/setting/barcode_setting.php';?>
            <!-- barcode_setting ED -->


            <!-- connect_setting OP -->
                <?php require_once '../app/views/setting/connect_setting.php';?>
            <!-- connect_setting ED -->


            <!-- idas_update OP -->
                <?php require_once '../app/views/setting/idas_update.php';?>
            <!-- idas_update ED -->


            <!-- <?php
                // // <!-- Controller_Setting -->
                // if(file_exists('../app/views/'.$data['div_controller_setting'].'.php')){
                //     require_once '../app/views/'.$data['div_controller_setting'].'.php';
                // }
                // // <!-- System_Setting -->
                // if(file_exists('../app/views/'.$data['div_system_setting'].'.php')){
                //     require_once '../app/views/'.$data['div_system_setting'].'.php';
                // }
                // // <!-- Barcode -->
                // if(file_exists('../app/views/'.$data['div_barcode_setting'].'.php')){
                //     require_once '../app/views/'.$data['div_barcode_setting'].'.php';
                // }
                // // <!-- Connect -->
                // if(file_exists('../app/views/'.$data['div_connect_setting'].'.php')){
                //     require_once '../app/views/'.$data['div_connect_setting'].'.php';
                // }
                // // <!-- iDas Update -->
                // if(file_exists('../app/views/'.$data['div_idas_update'].'.php')){
                //     require_once '../app/views/'.$data['div_idas_update'].'.php';
                // }
            ?> -->
        </div>
    </div>
</div>

<script>

    function time_save(){
        var newTime = document.getElementById('newTime').value;
        var device_id = <?php echo $data['controller_info']['device_id'];?>;

        //console.log(newTime);
        if(newTime){
            $.ajax({
                url: "?url=Settings/edit_system_date",
                method: "POST",
                data:{ 
                    device_id: device_id,
                    newTime: newTime

                },
                success: function(response){
                    alert(response);
                    //history.go(0);
                },
                error: function(xhr, status, error) {
                    
                }
            });       
        }

    }


    function button_save_password_gust(){

        var device_id = <?php echo $data['controller_info']['device_id'];?>;

        var pass_guest1 = document.getElementById('new_password_guest').value;
        var pass_guest2 = document.getElementById('comfirm_password_guest').value;

        //正規化 密碼格式(1個英文+1個數字,長度:4)
        var pattern = /^(?=.*[A-Za-z])(?=.*\d).{4,}$/;
        if(pass_guest1 == pass_guest2 && pattern.test(pass_guest1)){
            $.ajax({
                url: "?url=Admins/EditGuestPwd",
                method: "POST",
                data:{ 
                    device_id: device_id,
                    new_password: pass_guest1

                },
                success: function(response) {
                    alert(response);
                    history.go(0);
                },
                error: function(xhr, status, error) {
                    
                }
            });   
        }else{
            alert('密碼格式不符合要求');
        }
        
    }

    function OpenButton(ButtonMode) {
        const sections = {
            "Controller": "Controller_Setting",
            "System": "System_Setting",
            "Barcode": "Barcode_Setting",
            "Connect": "Connect_Setting",
            "Update": "iDas-Update_Setting"
        };

        const buttons = {
            "Controller": "bnt1",
            "System": "bnt2",
            "Barcode": "bnt3",
            "Connect": "bnt4",
            "Update": "bnt5"
        };

        // 隱藏所有區塊 + 移除按鈕 active 樣式
        for (const key in sections) {
            const sectionId = sections[key];
            const buttonId = buttons[key];
            document.getElementById(sectionId).style.display = "none";
            document.getElementById(buttonId).classList.remove("active");
        }

        // 顯示對應區塊 + 加上 active 樣式
        if (sections[ButtonMode] && buttons[ButtonMode]) {
            document.getElementById(sections[ButtonMode]).style.display = "";
            document.getElementById(buttons[ButtonMode]).classList.add("active");
        } else {
            console.warn("Unknown ButtonMode:", ButtonMode);
        }
    }

</script>    


<?php require APPROOT . 'views/inc/footer.php'; ?>