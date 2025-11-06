<?php require APPROOT . '/views/inc/header.php'; ?>
<style type="text/css">
    .form-control
    {
        width: auto!important;
        display: initial!important;
        padding:5px;
    }
    .form-control.is-invalid
    {
        padding-right:inherit!important;
    }
    .is-invalid~.invalid-feedback
    {
       display: inline!important;
    }

</style>

<div class="container.ms">
    <div class="w3-text-white w3-center">
        <table class="no-border">
            <tr id="header">
                <td width="100%"><h3><?php echo $text['step_management'];?></h3></td>
            </tr>
        </table>
    </div>

    <div class="main-content">
        <div class="center-content">
            <div class="topnav">
                <label style="font-size:20px;color: #000; padding-left: 2%" for="job_id"><?php echo $text['job_id'];?> :</label>&nbsp;&nbsp;
                <input type="text" id="job_id" name="job_id" size="8" maxlength="20" value="<?php echo $data['job_id'];?>" disabled
                style="height:28px; font-size:20px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">&nbsp;&nbsp;&nbsp;&nbsp;

                <label style="font-size:20px;color: #000" for="seq_id"><?php echo $text['seq_id']; ?>:</label>
                <input type="text" id="seq_id" name="seq_id" size="8" maxlength="20" value="<?php echo $data['sequence_id'];?>" disabled
                style="height:28px; font-size:20px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">

                <button id="back_btn" onclick="location.href = '?url=Sequences/index/<?php echo $data['job_id'];?>'"><?php echo $text['return'];?></button>
            </div>

            <div class="table-container">
                <table id="step_table" class="table w3-table">
                    <thead id="header-table">
                        <tr class="w3-dark-grey">
                            <th class="w3-center" width="10%"><?php echo $text['step_id']; ?></th>
                            <th class="w3-center" width="20%"><?php echo $text['step_name']; ?></th>
                            <th class="w3-center" width="10%"><?php echo $text['step_target_type']; ?> </th>
                            <th class="w3-center" width="20%"><?php echo $text['direction']; ?></th>
                            <th class="w3-center" width="20%"><?php echo $text['rpm']; ?></th>
                            <th class="w3-center" width="10%"><?php echo $text['up']; ?></th>
                            <th class="w3-center" width="10%"><?php echo $text['down']; ?></th>
                        </tr>
                    </thead>

                    <tbody style="font-size: 1.8vmin; text-align: center;">
                        <?php foreach ($data['step'] as $key => $value){?>
                            <tr>
                                <td><?= $value['StepSelect'] ?></td>
                                <td><?= $value['STEPname'] ?></td>
                                <td>
                                    <?php
                                        if ($value['StepOption'] == 1) {
                                            echo $text['angle'];
                                        } elseif ($value['StepOption'] == 2) {
                                            echo $text['torque'];
                                        } else {
                                            echo $text['time'];
                                        }
                                    ?>
                                </td>
                                <td><?= $value['StepDirection'] == 1 ? $text['CW'] : $text['CCW'] ?></td>
                                <td><?= $value['StepRPM'] ?></td>
                                <td><img src="./img/btn_up.png" onclick="MoveUp_seq(this);"></td>
                                <td><img src="./img/btn_down.png"onclick="MoveDown_seq(this);"></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        <div id="TotalPage">
            <div id="TotalStepTable">
                <div style="color:black; float: right; margin: 2px"><?php echo $text['total_step'];?> :
                    <label id="RecordCnt" name="RecordCnt" type="text" style="margin-right: 20px"><?php echo count($data['step']);?></label>
                </div>
            </div>
        </div>

        <div class="buttonbox">
            <?php $status = $data['total_step'] == 5 ? 'disabled' : ''; ?>
            <input id="S3" name="Step_Manager_Submit" type="button" value="<?php echo $text['New'];?>" tabindex="1"  onclick="cound_step('new');" <?php echo $status;?>>
            <input id="S6" name="Step_Manager_Submit" type="button" value="<?php echo $text['Edit'];?>" tabindex="1" onclick="cound_step('edit')">
            <input id="S5" name="Step_Manager_Submit" type="button" value="<?php echo $text['Copy'];?>" tabindex="1"  onclick="cound_step('copy');" <?php echo $status; ?>>
            <input id="S4" name="Step_Manager_Submit" type="button" value="<?php echo $text['Delete'];?>" tabindex="1" onclick="cound_step('del');" >
        </div>
    </div>

    <!-- Copy Modal -->
    <div id="copystep" class="modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content w3-animate-zoom" style="width: 60%">
                <header class="w3-container modal-header">
                    <span onclick="closebutton('copystep')"
                        class="w3-button w3-red w3-display-topright" style="width: 50px; margin: 3px;">&times;</span>
                    <h3 id='modal_title'><?php echo $text['copy_step'];?></h3>
                </header>

                <div class="modal-body">
                    <form id="copy_step_form">
                        <label class="col col-form-label" style="font-weight: bold">><?php echo $text['copy_from']; ?></label>
                        <div style="padding-left: 10px;">
                            <div class="row ">
                                <label class="t1 col-4 col-form-label"><?php echo $text['step_id']; ?>：</label>
                                <div class="col-5 t2">
                                    <input type="number" class="form-control" id="from_step_id" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="t1 col-4 col-form-label"><?php echo $text['step_name']; ?>：</label>
                                <div class="col-5 t2">
                                    <input type="text" class="form-control" id="from_step_name" disabled>
                                </div>
                            </div>
                        </div>

                        <label for="to_step_id" class="col col-form-label" style="font-weight: bold"><?php echo $text['copy_to']; ?></label>
                        <div style="padding-left: 10px;">
                            <div class="row mb-3">
                                <label class="t1 col-4 col-form-label"><?php echo $text['step_id']; ?>：</label>
                                <div class="col-5 t2">
                                    <input type="number" class="form-control" id="to_step_id" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="t1 col-4 col-form-label"><?php echo $text['step_name']; ?>：</label>
                                <div class="col-5 t2">
                                    <input type="text" class="form-control" id="to_step_name" >
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-center">
                    <button class="btn btn-primary" id="copy_step_save" onclick="copy_step_save()"><?php echo $text['save']; ?></button>
                    <button class="button-modal" onclick="closebutton('copystep')" class="closebtn"><?php echo $text['close']; ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- hiển thị cửa sổ thông báo -->
	<div id="spinner" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
		<div class="spinner-border text-primary" role="status">
			<span class="sr-only"></span>
		</div>
	</div>	

</div>
<script>
    
$(document).ready(function () {
    highlight_row('step_table');
});

document.addEventListener('DOMContentLoaded', function() {
  var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      var headerElements = document.querySelectorAll('.ajs-header');
      headerElements.forEach(function(headerElement) {
        headerElement.parentNode.removeChild(headerElement);
      });
    });
  });

  observer.observe(document.body, { childList: true, subtree: true });
});

// Get the modal
var modal = document.getElementById('newstep');

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
var stepid = '';
var rows = document.getElementsByTagName("tr");
for (var i = 0; i < rows.length; i++) {
    (function(row) {
        var cells = row.getElementsByTagName("td");
        if (cells.length > 0) {
            cells[0].addEventListener("click", function() {
           
                stepid   = cells[0] ? (cells[0].textContent || cells[0].innerText) : null;
                localStorage.setItem("stepid", stepid);
            });
        }
    })(rows[i]);
}

    /*
    $(document).ready(function () {
        // document.getElementsByClassName("tablink")[0].click();

        var modal = document.getElementById('StepNew');
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        const job_id = $("#job_id").val();
        let table = $('#Table_Step').DataTable({
            // paging: false,
            searching: false,
            bInfo : false,
            "ordering": false,
            // "bPaginate": false,
            "dom": "frti",
            "pageLength": 99,
            "language": {
               infoEmpty: "",
               emptyTable: "",
               zeroRecords: ''
            },
            columnDefs: [
                {
                    targets: [0,1,2,3,4],
                }
              ]
            });
        $('#Table_Step tbody').on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });

        let data = table.rows().data();
        let page_info = table.page.info();

        $('#RecordCnt').val(data.length);
        $('#CurrentPage').val(page_info.page + 1);
        $( "input[name='TotalPage']" ).val( page_info.pages);

    });
   

    function crud_job(argument) {

        const job_id = $("#job_id").val();
        const seq_id = $("#seq_id").val();
        let step_id;
        try {
          step_id = document.querySelector("#Table_Step tr.selected").childNodes[0].textContent;//取得第一欄的值step_id
        } catch (error) { 
          step_id = null;  //任意默认值都可以被使用 
    
        };
        try { 
          step_name = document.querySelector("#Table_Step tr.selected").childNodes[1].textContent;//取得第一欄的值step_name
        } catch (error) { 
          step_name = null;  //任意默认值都可以被使用 
        };

        
        if(argument == 'new'){
            new_ntcs_step(job_id,seq_id);
        }
        if(argument == 'del' && step_id != null){
            delete_step(job_id,seq_id,step_id);
        }
        if(argument == 'edit' && step_id != null){
            edit_ntcs_step(job_id,seq_id,step_id);
        }
        if(argument == 'copy' && step_id != null){
            copy_step(job_id,seq_id,step_id,step_name);
        }

    }
    */

    //function row swap
    /*
    function swap_row(row,direction) {
        let table = row.parentNode;
        let origin_index = row.childNodes[0].textContent;

        if(direction == 'up'){
            pre_row = row.previousSibling;
            pre_index = pre_row.childNodes[0].textContent;
            step_id2 = pre_index;
        }
        if(direction == 'down'){
            next_row = row.nextSibling;
            next_index = next_row.childNodes[0].textContent;
            step_id2 = next_index;
        }

        var formData = {
            job_id: $("#job_id").val(),
            seq_id: $("#seq_id").val(),
            step_id1: origin_index,
            step_id2: step_id2,
        };

        $.ajax({
          type: "POST",
          url: "?url=Steps/swap_advancedstep",
          data: formData,
          dataType: "json",
          encode: true,
          beforeSend: function() {
            $('#overlay').removeClass('hidden');
          },
        }).done(function (data) {//成功且有回傳值才會執行
          // console.log(data);
          $('#overlay').addClass('hidden');
          if(data['error_message'] != ''){
            alert(data['error_message']);   
          }else{
            if(direction == 'up'){
                table.insertBefore(row, get_previoussibling(row));
                pre_row.childNodes[0].textContent = origin_index;
                row.childNodes[0].textContent = pre_index;
            }
            if(direction == 'down'){
                table.insertBefore(row, get_nextsibling(get_nextsibling(row)));
                next_row.childNodes[0].textContent = origin_index;
                row.childNodes[0].textContent = next_index;
            }
            // location.reload();   


          }
        });

    }
    */

    

</script>

<//?php if($_SESSION['privilege'] != 'admin'){ ?>
<script>
/*  
  $(document).ready(function () {
    disableAllButtonsAndInputs()
    document.getElementById("return").disabled = false;
    document.getElementById("S6").disabled = false; //edit button
    document.getElementById("S1").disabled = false; //first
    document.getElementById("S2").disabled = false; //previous
    document.getElementById("S7").disabled = false; //next
    document.getElementById("S8").disabled = false; //last
    document.getElementsByClassName("btn-close")[0].disabled = false;//btn-close

    var buttons = document.getElementsByClassName("button_sequence");
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].disabled = false;
    }
  });
*/
</script>
<//?php } ?>

<?php require APPROOT . 'views/inc/footer.php'; ?>