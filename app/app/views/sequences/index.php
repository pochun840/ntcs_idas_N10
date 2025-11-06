<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container-ms">
	<div class="w3-text-white w3-center">
        <table class="no-border">
            <tr id="header">
                <td width="100%"><h3><?php echo $text['seq_management'];?></h3></td>
            </tr>
        </table>
    </div>
    <div class="main-content">
        <div class="center-content">
			<div class="topnav">
                <label style="font-size:20px;color: #000; padding-left: 2%" for="job_id"><?php echo $text['job_id'];?> :</label>&nbsp;&nbsp;
                <input type="text" id="job_id" name="job_id" size="10" maxlength="20" value="<?php echo $data['job_id'];?>" disabled
                style="height:28px; font-size:20px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">

                <button id="back_btn" type="button" onclick="window.location.href='?url=Jobs/index'"><?php echo $text['return'];?></button>
            </div>

            <div class="btn">
            	<img src="..\public\img\btn_plusseq.png" style="height: 35px; border: 2px solid black;cursor: pointer;border-radius:50%; ">
            	<button class="btn btn-secondary" onclick="location.href = '?url=Sequences/Add_Sequence/<?php echo $data['job_id'];?>/1'"><?php echo $text['sequence_type'][1]; ?></button>
            	<button class="btn btn-secondary" onclick="location.href = '?url=Sequences/Add_Sequence/<?php echo $data['job_id'];?>/2'"><?php echo $text['sequence_type'][2]; ?></button>
            	<button class="btn btn-secondary" onclick="location.href = '?url=Sequences/Add_Sequence/<?php echo $data['job_id'];?>/3'"><?php echo $text['sequence_type'][3]; ?></button>
            	<button class="btn btn-secondary" onclick="location.href = '?url=Sequences/Add_Sequence/<?php echo $data['job_id'];?>/4'"><?php echo $text['sequence_type'][4]; ?></button>
            	<button class="btn btn-secondary" onclick="location.href = '?url=Sequences/Add_Sequence/<?php echo $data['job_id'];?>/5'"><?php echo $text['sequence_type'][5]; ?></button>
            </div>

        	<div class="table-container">
        	    <div class="scrollbar" id="style-seq">
                    <div class="force-overflow">
                		<table id="seq_table" class="table w3-table">
                			<thead id="header-table">
                				<tr class="w3-dark-grey">
                	                <th><?php echo $text['seq_id']; ?></th>
                	                <th><?php echo $text['seq_name']; ?></th>
                	                <th><?php echo $text['target_type']; ?></th>
                	                <th><?php echo $text['tightening_repeat']; ?></th>
                	                <th><?php echo $text['enable']; ?></th>
                	                <th><?php echo $text['up']; ?></th>
                	                <th><?php echo $text['down']; ?></th>
                	                <th><?php echo $text['total_step']; ?></th>
                	                <th><?php echo $text['add_step']; ?></th>
                	                <th style="display: none;"><?php echo $text['target_type']; ?></th>
                	            </tr>
                			</thead>
                			<tbody style="font-size: 1.8vmin;text-align: center;">
								<?php $sequenceCount = isset($data['sequences']) ? count($data['sequences']) : 0; ?>
								<?php foreach ($data['sequences'] as $key => $value) { ?>
									<tr>
										<td><?php echo $value['SEQID']; ?></td>
										<td><?php echo $value['SEQname']; ?></td>
										<td data-type="<?php echo $value['type']; ?>" data-seqid="<?php echo $value['SEQID']; ?>">
											<?php echo $text['sequence_type'][$value['type']] ?? ''; ?>
										</td>
										<td ><?php echo $value['seq_repeat']; ?></td>
										
										<td><input class="seq_enable" style="zoom:1.5; vertical-align: middle" data-sequence-id="<?php echo $value['SEQID']; ?>" id="sequence_enable" value="<?php echo ($value['skip'] == 0 ? '1' : '0'); ?>" type="checkbox" <?php echo ($sequenceCount > 1 ? 'onclick="updateValue(this)"' : 'disabled'); ?> <?php echo ($value['skip'] == 0 ? 'checked' : ''); ?>></td>
                 						<td><img src="./img/btn_up.png" onclick="MoveUp(this);"></td>
										<td><img src="./img/btn_down.png" onclick="MoveDown(this);"></td>
										<td><?php echo $value['total_step']; ?></td>
										<td>
											<?php if ($value['type'] == 1): ?>
												<?php $url = '?url=Steps/index/' . $data['job_id'] . '/' . $value['SEQID']; ?>
												<img id="Add_Step" src="./img/btn_plus.png" onclick="location.href='<?php echo $url; ?>'">
											<?php endif; ?>
										</td>
										<td style="display:none;"><?php echo $value['type']; ?></td>
									</tr>
								<?php } ?>
                			</tbody>
                		</table>
                    </div>
        	    </div>
	        </div>
        </div>
    </div>

    <div class="footer">
		<div id="TotalPage">
            <div id="TotalSeqTable">
                <div style="color:black; float: right; margin: 2px"><?php  echo $text['total_seq']; ?> :
                    <label id="RecordCnt" name="RecordCnt" type="text" style="margin-right: 20px"><?php echo count($data['sequences']); ?></label>
                </div>
            </div>
        </div>

        <div class="buttonbox">
			<?php  $status = $data['total_seq']  >  100 ? 'disabled' : ''; ?>
            <input id="S6" name="Job_Manager_Submit" type="button" value="<?php echo $text['Edit']; ?>" tabindex="1" onclick="cound_seq('edit')">
            <input id="S5" name="Job_Manager_Submit" type="button" value="<?php echo $text['Copy']; ?>" tabindex="1" onclick="cound_seq('copy')">
            <input id="S4" name="Job_Manager_Submit" type="button" value="<?php echo $text['Delete']; ?>" tabindex="1" onclick="cound_seq('del')">
        </div>
    </div>

	<!-- Seq Modal -->
	<div id="SeqModal" class="modal">
	  	<div class="modal-dialog modal-dialog-centered modal-lg">
	    	<div class="modal-content w3-animate-zoom" style="width: 60%">
				<header class="w3-container modal-header">
					<span onclick="closebutton('SeqModal');"
					class="w3-button w3-red w3-display-topright" style="width: 50px; margin: 3px;">&times;</span>
					<h3 id='modal_title'>New Sequence</h3>
				</header>

				<div class="modal-body">
					<form id="new_seq_form">
						<div class="row mb-3">
							<label for="sequence_id" class="col-sm-5 col-form-label"><?php echo $text['seq_id']; ?>：</label>
							<div class="col-sm-5">
								<input type="number" class="form-control" id="sequence_id" disabled>
							</div>
						</div>
						<div class="row mb-3">
							<label for="sequence_name" class="col-sm-5 col-form-label"><?php echo $text['seq_name']; ?>：</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="sequence_name" oninput="validateInput_seq_name('sequence_name')" maxlength="12">
								<div class="invalid-feedback"><?php echo $error_message['sequence_name']; ?></div>
							</div>
						</div>
						<div class="row mb-3">
							<label for="TR" class="col-sm-5 col-form-label"><?php echo $text['tightening_repeat']; ?>：</label>
							<div class="col-sm-5">
								<input type="number" class="form-control" id="TR" min="0" max="99" oninput="validateInput_TP('TR')">
								<div class="invalid-feedback"><?php echo $error_message['tightening_repeat']; ?></div>
							</div>
						</div>
						<div class="row mb-3">
							<label for="ng_stop" class="col-sm-5 col-form-label"><?php echo $text['NG_Stop']; ?>：</label>
							<div class="col-sm-5">
								<select id="ng_stop">
									<option value="" disabled selected><?php echo $text['Choose_option']; ?></option>
									<option value="0"><?php echo $text['option_no']; ?></option>
									<option value="1">01</option>
									<option value="2">02</option>
									<option value="3">03</option>
									<option value="4">04</option>
									<option value="5">05</option>
									<option value="6">06</option>
									<option value="7">07</option>
									<option value="8">08</option>
									<option value="9">09</option>
								</select>
							</div>
						</div>
						<div class="row mb-3">
							<legend for="ok_job" class="col-sm-5 col-form-label pt-0"><?php echo $text['OK_Sequence']; ?>：</legend>
							<div class="col-sm-5">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="ok_seq_option" id="ok_seq_off" value="0">
									<label class="form-check-label" for="ok_seq_off"><?php echo $text['switch_off']; ?></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="ok_seq_option" id="ok_seq_on" value="1">
									<label class="form-check-label" for="ok_seq_on"><?php echo $text['switch_on']; ?></label>
								</div>
							</div>
						</div>
						<div class="row mb-3">
							<label  class="col-sm-5 col-form-label pt-0"><?php echo $text['OK_Sequence_Stop']; ?>：</label>
							<div class="col-sm-5">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="ok_seq_stop_option" id="ok_seq_stop_off" value="0">
									<label class="form-check-label" for="ok_seq_stop_off"><?php echo $text['switch_off']; ?></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="ok_seq_stop_option" id="ok_seq_stop_on" value="1">
									<label class="form-check-label" for="ok_seq_stop_on"><?php echo $text['switch_on']; ?></label>
								</div>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-5 col-form-label"><?php echo $text['Timeout']; ?>：</label>
							<div class="col-sm-5">
								<input type="number" class="form-control" id="timeout" min="0" max="60" oninput="restrictInput_timeout('timeout')">
								<div class="invalid-feedback"><?php echo $error_message['timeout']; ?></div>
							</div>
							<div class="col-sm-2 col-form-label" style="padding-left:0;"><?php echo $text['Second']; ?></div>
						</div>			  
					</form>
				</div>

				<div class="modal-footer justify-content-center">
					<button type="button" class="button-modal" id="new_seq_save" onclick="new_seq_save()"><?php echo $text['save']; ?></button>
					<button type="button" class="button-modal" onclick="closebutton('SeqModal');" class="closebtn"><?php echo $text['close']; ?></button>
				</div>
			</div>
	  	</div>
	</div>

	<!-- Copy Sequence -->
    <div id="copyseq" class="modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content w3-animate-zoom" style="width: 60%">
                <header class="w3-container modal-header">
                    <span onclick="closebutton('copyseq');"
                        class="w3-button w3-red w3-display-topright" style="width: 50px; margin: 3px;">&times;</span>
                    <h3 id='modal_title'><?php echo $text['Copy_Sequence'];?></h3>
                </header>

                <div class="modal-body">
                    <form id="new_seq_form">
        	            <label for="from_seq_id" class="col col-form-label" style="font-weight: bold"><?php echo $text['copy_from'];?></label>
        	            <div style="padding-left: 10%">
        		            <div class="row">
        				        <label for="from_seq_id" class="t1 col-4 col-form-label"><?php echo $text['seq_id'];?> :</label>
        				        <div class="col-5 t2 ">
        				            <input type="text" class="form-control" id="from_seq_id" disabled>
        				        </div>
        				    </div>
        				    <div class="row">
        				        <label for="from_seq_name" class="t1 col-4 col-form-label"><?php echo $text['seq_name'];?> :</label>
        				        <div class="t2 col-5">
        				            <input type="text" class="form-control" id="from_seq_name" disabled>
        				        </div>
        				    </div>
        			    </div>

        			    <label for="from_seq_id" class="col col-form-label" style="font-weight: bold"><?php echo $text['copy_to'];?></label>
        			    <div style="padding-left: 10%">
        				    <div class="row">
        				        <label for="to_seq_id" class="t1 col-4 col-form-label"><?php echo $text['seq_id'];?> :</label>
        				        <div class="t2 col-5">
        				            <input type="number" class="form-control" id="to_seq_id" value ='<?php echo $data['next_seq_id'];?>'>
        				        </div>
        				    </div>
        				    <div class="row">
        				        <label for="to_seq_name" class="t1 col-4 col-form-label"><?php echo $text['seq_name'];?> :</label>
        				        <div class="t2 col-5">
        				            <input type="text" class="form-control" id="to_seq_name" value='<?php echo "SEQ-".$data['next_seq_id'];?>'>
        				        </div>
        				    </div>
        			    </div>
        			  </form>
                </div>

                <div class="modal-footer justify-content-center">
                    <button id="" class="button-modal" onclick="copy_seq_by_id()"><?php echo $text['save'];?></button>
                    <button id="" class="button-modal" onclick="closebutton('copyseq');" class="closebtn"><?php echo $text['close'];?></button>
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


<script type="text/javascript">

// Change the color of a row in a table
$(document).ready(function () {
    highlight_row('seq_table');
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
var seqid = '';
var seq_name = '';
var modal = document.getElementById('SeqModal');
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

</script>
<?php require_once '../app/views/sequences/seq_share.php';?>

