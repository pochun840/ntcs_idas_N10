<script>
var rowInfoArray = [];

var seq_id = ''; 
var seq_name = '';

<?php if(!empty($data['sequences'])){?>
    <?php foreach($data['sequences'] as $key =>$val) {?>
        var sequenceId = "<?php echo $val['SEQID'];?>";
        var sequenceName = "<?php echo $val['SEQname'];?>";

        var exists = rowInfoArray.some(function(item) {
            return item.sequence_id === sequenceId || item.sequence_name === sequenceName;
        });

        if (!exists) {
            var rowInfo = {
                sequence_id: sequenceId,
                sequence_name: sequenceName
            };
            rowInfoArray.push(rowInfo);
        }
    <?php } ?>
<?php } ?>


function cound_seq(argument) {
    const job_id = document.getElementById("job_id")?.value;
    const table = document.getElementById('seq_table');
    if (!table) return;

    const selectedRow = table.querySelector('.selected');
    const seqnameVal = selectedRow?.cells[1]?.innerText ?? null;


    //if (!selectedRow && ['del', 'edit', 'copy'].includes(argument)) return;


    const seq_id = selectedRow?.cells[0]?.innerText.trim() ?? null;
    const seq_name = selectedRow?.cells[1]?.innerText.trim() ?? null;
    const target_type = selectedRow?.cells[2]?.innerText.trim() ?? null;
    const TR = selectedRow?.cells[3]?.innerText.trim() ?? null;
    let seq_type = selectedRow?.cells[9]?.innerText.trim() ?? null; // giữ nguyên

    // 👉 Thêm đoạn này để map từ tên => số (không đổi biến seq_type)
    const typeMap = {
        "Fastening": "1",
        "Message": "2",
        "Delay": "3",
        "Input": "4",
        "Output": "5"
    };
    if (seq_type in typeMap) {
        seq_type = typeMap[seq_type];
    }

    window.seq_id = seq_id;
    window.seq_name = seqnameVal;

    const needSeq = ['del', 'edit', 'copy'];

    // 若需要 seq_id 的操作卻沒選取列 - Ruò xūyào seq_id de cāozuò què méi xuǎnqǔ liè
    if (needSeq.includes(argument) && !seq_id) {
        if (typeof pleaseSelectJobTitle !== 'undefined' && typeof pleaseSelectJobMsg !== 'undefined') {
            alertify.alert(pleaseSelectJobTitle, pleaseSelectJobMsg);
        } else {
            alertify.alert("Notification", "Please select a seq"); // fallback tạm thời
        }
        return;
    }


    if (argument === 'new' || (needSeq.includes(argument) && seq_id !== null)) {
        document.querySelector(".main-content")?.classList.add("overlay-active");
    }

    switch (argument) {
        case 'new':
            new_seq(job_id);
            break;
        case 'del':
            if (seq_id) delete_seq(job_id, seq_id);
            break;
        case 'edit':
            if (seq_id) edit_sequence(seq_id, seq_type);
            break;
        case 'copy':
            if (seq_id) copy_seq(seq_id, seqnameVal);
            break;
        default:
            console.warn(`未知的操作類型: ${argument}`);
    }
}

var rows = document.getElementsByTagName("tr");
for (var i = 0; i < rows.length; i++) {
    (function(row) {
        var cells = row.getElementsByTagName("td");
        if (cells.length > 0) {
            cells[0].addEventListener("click", function(){
            seq_id = cells[0] ? (cells[0].textContent || cells[0].innerText) : null;
            seq_name  = seq_name = cells[1] ? (cells[1].textContent || cells[1].innerText) : null;
                
            });
        }
    })(rows[i]);
}


function sendRowInfoArray() {
    var jobid = '<?php echo $data['job_id']?>';
    var dataToSend = {
        jobid: jobid,
        rowInfoArray: rowInfoArray
    };
    
    $.ajax({
        url: "?url=Sequences/adjustment_order", 
        method: "POST",
        data: dataToSend,
        success: function(response) {
            history.go(0); 
        },
        error: function(xhr, status, error) {
            console.error('Error sending data:', error);
        }
    });
}

function copy_seq(seq_id, seq_name) {
    document.getElementById('copyseq').style.display = 'block';
    document.getElementById('from_seq_id').value = seq_id;
    document.getElementById('from_seq_name').value = seq_name;
}


function copy_seq_by_id() {

    var job_id = '<?php echo $data['job_id'];?>';
    var oldseqname = seq_name;
    var newseqid = document.getElementById('to_seq_id').value;
    var newseqname = document.getElementById("to_seq_name").value;    

    var language = getCookie('language');
    if(language == "zh-cn"){
        var text_info ='你确定吗？';
    }else if(language == "zh-tw"){
        var text_info ='你確定嗎 ?';
    }else{
        var text_info ='Are you sure ?';
    }

    if(newseqname){
        $.ajax({
            url: "?url=Sequences/check_seq_type",
            method: "POST",
            data:{ 
                job_id:job_id,
                newseqid: newseqid

            },
            success: function(response) {
                alertify.confirm(text_info, function (result) {
                if(result){
                    $.ajax({
                        url: "?url=Sequences/copy_seq_data",
                        method: "POST",
                        data:{
                            job_id: job_id,
                            seq_id: seq_id,
                            oldseqname: oldseqname,
                            newseqid: newseqid,
                            newseqname: newseqname
                        },
                        success: function(response){
                            console.log(response);
                            var responseData = JSON.parse(response);
                            alertify.alert(responseData.res_type, responseData.res_msg, function(){
                                history.go(0);
                            });
                           
                        },
                        error: function(xhr, status, error){
                            
                        }
                    });
                }else {
                    alertify.error('Cancelled');
                    // 用户点击取消按钮的处理逻辑
                }
                });
                        },
            error: function(xhr, status, error) {
                
            }
        });
          
    }

}

function getCookie(name) {
    var nameEQ = name + "=";
    //alert(document.cookie);
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1);
        if (c.indexOf(nameEQ) != -1) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// 刪除seq
function delete_seq(job_id, seq_id) {
    if (!job_id) return;

    var language = getCookie('language');
    var text_info, title;

    if (language === "zh-cn") {
        text_info = '你确定要删除这个工序吗？';
        title = '删除作业';
    } else if (language === "zh-tw") {
        text_info = '你確定要刪除這個工序嗎？';
        title = '刪除作業';
    } else {
        text_info = 'Are you sure you want to delete this sequence?';
        title = 'Delete sequence';
    }

    alertify.confirm(title, text_info, function () {
        // 點擊確認才會執行 AJAX
        document.querySelector(".main-content").classList.add("overlay-active");
        document.getElementById("spinner").style.display = 'block';

        $.ajax({
            url: "?url=Sequences/delete_seq_by_id",
            method: "POST",
            data:{ 
                jobid: job_id,
                seqid: seq_id
            },
            success: function (response) {
                success_response(response, 'spinner', true); // 自動關閉
                //location.reload();
            },
            error: function (xhr, status, error) {
                alertify.error("Delete failed: " + error);
                document.querySelector(".main-content").classList.remove("overlay-active");
                document.getElementById("spinner").style.display = 'none';
            }
        });

    }, function () {
        document.querySelector(".main-content").classList.remove("overlay-active");
    });
}


function getSelectedValue(name, defaultValue = 0) {
    const selected = document.querySelector(`input[name="${name}"]:checked`);
    return selected?.value ?? defaultValue;
}

function updateValue(element){
    var jobid = '<?php echo $data['job_id']?>';
    var type_value = element.checked ? 0 : 1 ;
    var seqid = element.getAttribute('data-sequence-id');

    if(seqid){
        $.ajax({
            url: "?url=Sequences/check_seq_enable", 
            method: "POST",
            data: { 
                jobid: jobid,
                seqid: seqid,
                skip: type_value
            },
            success: function(response) {
                console.log(response);
                //history.go(0);
            },
            error: function(xhr, status, error) {
                console.error('AJAX 错误:', status, error); 
            }
        });    
    }
}

//validate form
function form_seq_validate() {		
    var sequence_id = document.getElementById("sequence_id");
    var sequence_name = document.getElementById("sequence_name");
    var tightening_repeat = document.getElementById("TR");
    var timeout = document.getElementById("timeout");

	var isFormValid = true; // 表单验证状态，默认为通过

	// 验证 Seq Name 字段
	if (sequence_name.value.trim() === "") {
	    sequence_name.classList.add("is-invalid");
	    isFormValid = false;
	} else {
	    sequence_name.classList.remove("is-invalid");
	}

	// 验证 TP
	var TPValue = parseInt(tightening_repeat.value);
	if (isNaN(TPValue) || TPValue > 99 || TPValue < 1) {
	    tightening_repeat.classList.add("is-invalid");
	    isFormValid = false;
	} else {
	    tightening_repeat.classList.remove("is-invalid");
	}

	// 验证 Unfasten Force 字段
	var timeouteValue = parseInt(timeout.value);
	if (isNaN(timeouteValue) || timeouteValue < 0.1 || timeouteValue > 60) {
	    timeout.classList.add("is-invalid");
	    isFormValid = false;
	} else {
	    timeout.classList.remove("is-invalid");
	}

	return isFormValid;
}

//驗證job name
function validateInput_seq_name(input_id) {
	const inputElement = document.getElementById(input_id);
	inputElement.addEventListener('input', function(event) {
	  let inputValue = event.target.value;
		  
    // 移除特殊字符 只留-
    inputValue = inputValue.replace(/[^\a-\z\A-\Z0-9\u4E00-\u9FA5\- ]/g, '');
		  
	// 限制字符串长度为10个字符
	if (inputValue.length > 10) {
	    inputValue = inputValue.slice(0, 10);  // 截断字符串到限制长度
	    }
		  
		// 更新输入框的值
		event.target.value = inputValue;
	});
}


//驗證TP 的範圍
function validateInput_TP(input_id) {
	var input = document.getElementById(input_id).value;

	// 使用正则表达式验证输入是否为数字，并处于范围 1 到 99 之间
	var pattern = /^(?:[1-9]|[1-9][0-9])$/;
	input.value = 99; // 将输入框的值设为0
	if (pattern.test(input)) {
	    // console.log("Input is valid.");
	    // 在这里执行其他操作，例如更新第二个输入框的值
	} else {
	    // console.log("Input is invalid. Please enter a number between 1 and 99.");
	    input.value = 0; // 将输入框的值设为0
	}
	if (input.length > 2) {
	    ss = input.slice(0, 2); // 仅保留前两个字符
	    document.getElementById(input_id).value = ss
	}

}


//限制timeout
function restrictInput_timeout(input_id) {
    // 获取输入的值
    let decimalPlaces = 1;
	var inputValue = document.getElementById(input_id).value;

	var regex = new RegExp("^\\d+(\\.\\d{0," + decimalPlaces + "})?$");
	  
	if (!regex.test(inputValue)) {
	    // 如果输入不符合要求，则截取合法的部分
	    var decimalValue = parseFloat(inputValue).toFixed(decimalPlaces);
	    event.target.value = decimalValue;
	}else{
	if (inputValue < 0.1) {
	    inputValue = 0.1;
	    event.target.value = '';	
        } else if (inputValue > 60.0) {
            inputValue = 60.0;
            event.target.value = inputValue;	
        }
		    
	}

}

function setRadioButton_value(radioButtons, value) {
    radioButtons.forEach(function(button) {
        if (button.value === value.toString()) {
            button.checked = true;
        } else {
            button.checked = false;
        }
    });
}

//validate form
function form_validate() {
    // body...
    var stepNameInput = document.getElementById("to_step_name");
    var isFormValid = true; // 表单驗證状态，默认为通过

    // 驗證 step name 字段
    if (stepNameInput.value.trim() === "") {
        stepNameInput.classList.add("is-invalid");
        isFormValid = false;
    } else {
        stepNameInput.classList.remove("is-invalid");
    }
    return isFormValid;
}



/*============================================================================================*/

function seq_fastening_check() {
  // === 多語詞庫 ===
  const I18N = {
    'en-us': {
      dialogTitle: 'Warning',
      empty: '“{label}” cannot be empty.',
      format: '“{label}” contains invalid characters.',
      range: '“{label}” must be between {min} and {max}.',
      minOnly: '“{label}” must be ≥ {min}.',
      maxOnly: '“{label}” must be ≤ {max}.',
      ok: 'OK',
      labels: {
        SEQname: 'Sequence Name',
        seq_repeat: 'Repeat',
        timeout: 'Timeout (s)',
        dt_time: 'DT Time (s)',
        tt_time: 'TT Time (s)',
        unscrew_rpm: 'Reverse RPM',
        unscrew_torque_threshold: 'Reverse Torque Threshold',
        unscrew_angle_threshold: 'Reverse Angle Threshold',
        unscrew_force: 'Reverse Force (%)'
      }
    },
    'zh-tw': {
      dialogTitle: '警告',
      empty: '{label} 不可為空白。',
      format: '{label} 含有不允許的字元。',
      range: '{label} 必須介於 {min} ~ {max} 之間。',
      minOnly: '{label} 必須 ≥ {min}。',
      maxOnly: '{label} 必須 ≤ {max}。',
      ok: '確定',
      labels: {
        SEQname: '工序名稱',
        seq_repeat: '顆數',
        timeout: '超時鎖附(秒)',
        dt_time: '顆數間隔時間(秒)',
        tt_time: '工序完成時間(秒)',
        unscrew_rpm: '拆螺絲轉速 (RPM)',
        unscrew_torque_threshold: '門檻點扭力',
        unscrew_angle_threshold: '門檻點角度',
        unscrew_force: '反轉力度 (%)'
      }
    },
    'zh-cn': {
      dialogTitle: '警告',
      empty: '{label} 不能为空。',
      format: '{label} 包含不允许的字符。',
      range: '{label} 必须介于 {min} ~ {max} 之间。',
      minOnly: '{label} 必须 ≥ {min}。',
      maxOnly: '{label} 必须 ≤ {max}。',
      ok: '确定',
      labels: {
        SEQname: '工序名称',
        seq_repeat: '颗数',
        timeout: '超时锁附(秒)',
        dt_time: '颗数间隔时间(秒)',
        tt_time: '工序完成时间(秒)',
        unscrew_rpm: '拆螺丝转速 (RPM)',
        unscrew_torque_threshold: '门槛点扭力',
        unscrew_angle_threshold: '门槛点角度',
        unscrew_force: '反转力度 (%)'
      }
    }
  };

  // --- 語系 ---
  function normalizeLang(raw) {
    const x = String(raw || '').toLowerCase();
    if (!x) return 'en-us';
    if (x === 'en') return 'en-us';
    if (x.startsWith('zh')) {
      if (x.includes('tw') || x.includes('hk') || x.includes('mo') || x.includes('hant')) return 'zh-tw';
      if (x.includes('cn') || x.includes('sg') || x.includes('hans')) return 'zh-cn';
      return 'zh-tw';
    }
    return x;
  }
  const langCookie = (typeof getCookie === 'function' && getCookie('language')) ||
                     document.documentElement.getAttribute('lang') || 'en-us';
  const lang = normalizeLang(langCookie);
  const dict = I18N[lang] || I18N['en-us'];

  const t = (key, params = {}) => {
    let s = dict[key] ?? I18N['en-us'][key] ?? key;
    for (const [k, v] of Object.entries(params)) s = s.replaceAll(`{${k}}`, String(v));
    return s;
  };
  const labelOf = (id) =>
    document.getElementById(id)?.getAttribute('data-label') ||
    dict.labels?.[id] || id;

  // === 彈窗（含 alert() 後備） ===
  const alertPopup = (msg, el) => {
    const hasAlertify = typeof window.alertify !== 'undefined' && typeof window.alertify.alert === 'function';
    if (hasAlertify) {
      try { if (alertify?.defaults?.glossary) alertify.defaults.glossary.ok = dict.ok; } catch (e) {}
      alertify
        .alert(dict.dialogTitle, msg, () => el && el.focus())
        .set('movable', false)
        .set('labels', { ok: dict.ok });
    } else {
      window.alert(`${dict.dialogTitle}\n\n${msg}`);
      if (el) try { el.focus(); } catch (e) {}
    }
  };

  // === UI 助攻 ===
  const markAndFocusInvalid = (el) => {
    if (!el) return;
    el.classList.add('is-invalid');
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => { try { el.focus({ preventScroll: true }); } catch (e) {} }, 0);
  };

  // === 讀取工具規格（供範圍用） ===
  const Tool_Max_Torque = Number(document.getElementById('tool_max_torque')?.value ?? '');
  const Tool_Min_Torque = Number(document.getElementById('tool_min_torque')?.value ?? '');
  const Tool_Max_RPM    = Number(document.getElementById('tool_max_rpm')?.value ?? '');
  const Tool_Min_RPM    = Number(document.getElementById('tool_min_rpm')?.value ?? '');

  // === 模式判斷（會影響某些欄位是否必填/檢查） ===
  const selectedForceMode = document.querySelector('input[name="unscrew_forcemode"]:checked')?.value ?? null; // '0' or others
  const isAutoMode        = !!document.getElementById('unscrew_mode_auto')?.checked;
  const unscrewModeVal    = document.querySelector('input[name="unscrew_mode"]:checked')?.value ?? null; // '0'/'1'...

  // === 扭力單位精度推斷（影響 threshold 輸入格式） ===
  const UNIT_DECIMALS = { 0: 2, 1: 3, 2: 2, 3: 4, 4: 1 }; // 0:kgf.cm,1:N·m,2:lbf.in,3:kgf.m,4:cN·m
  const unitEl        = document.getElementById('seq_unit_code');
  const torque_unit   = parseInt(unitEl?.value ?? 1, 10);
  const baseDecimals  = Object.prototype.hasOwnProperty.call(UNIT_DECIMALS, torque_unit) ? UNIT_DECIMALS[torque_unit] : 3;
  const roundDecimals = baseDecimals + 1;
  const torqueThreshPattern = new RegExp(`^\\d{1,6}(?:\\.\\d{1,${roundDecimals}})?$`);

  // === 欄位規則 ===
  const rules = [
    { id: 'SEQname',                    pattern: /^[a-zA-Z0-9\u4E00-\u9FA5\-]+$/       , min: null            , max: null             },
    { id: 'seq_repeat',                 pattern: /^\d{1,4}$/                           , min: 1               , max: 99               },
    { id: 'timeout',                    pattern: /^\d{1,5}$/                           , min: 0               , max: 60               },
    { id: 'dt_time',                    pattern: /^\d{1,5}$/                           , min: 0               , max: 99               },
    { id: 'tt_time',                    pattern: /^\d{1,5}$/                           , min: 0               , max: 6000             },
    { id: 'ng_stop',                    pattern: /^\d{1,5}$/                           , min: 0               , max: 9                },
    { id: 'unscrew_rpm',                pattern: /^\d+$/                               , min: Tool_Min_RPM    , max: Tool_Max_RPM     },
    { id: 'unscrew_torque_threshold',   pattern: torqueThreshPattern                   , min: 0               , max: Tool_Max_Torque  },
    { id: 'unscrew_angle_threshold',    pattern: /^\d+(?:\.\d{1})?$/                   , min: 0               , max: 30600            },
    { id: 'unscrew_force',              pattern: /^\d+$/                               , min: 0               , max: 100              }
  ];

  // === 檢查：逐一驗證，第一個錯誤就彈窗並回傳 false ===
  for (const r of rules) {
    const el = document.getElementById(r.id);
    if (!el) continue;

    // Auto 模式跳過反轉相關欄位
    if (isAutoMode && ['unscrew_torque_threshold','unscrew_angle_threshold','unscrew_force','unscrew_rpm'].includes(r.id)) {
      el.classList.remove('is-invalid');
      continue;
    }
    // forcemode != 0 時，unscrew_force 不檢查
    if (r.id === 'unscrew_force') {
      if (selectedForceMode !== '0') { el.classList.remove('is-invalid'); el.disabled = true; continue; }
      el.disabled = false;
    }

    let val = (el.value ?? '').trim();

    // unscrew_mode == 0：允許多一位輸入，但提交前回填基準位數
    if (r.id === 'unscrew_torque_threshold' && unscrewModeVal === '0' && val !== '') {
      const n = Number(val);
      if (Number.isFinite(n)) {
        const fixed = Number(n.toFixed(baseDecimals)).toFixed(baseDecimals);
        el.value = fixed;
        val = fixed;
      }
      el.setAttribute('pattern', `^\\d{1,6}(?:\\.\\d{1,${roundDecimals}})?$`);
      el.setAttribute('inputmode', 'decimal');
      el.setAttribute('step', String(1 / Math.pow(10, baseDecimals)));
    }

    // 空值
    if (val === '') {
      markAndFocusInvalid(el);
      alertPopup(t('empty', { label: labelOf(r.id) }), el);
      return false;
    }

    // 格式
    if (!r.pattern.test(val)) {
      markAndFocusInvalid(el);
      alertPopup(t('format', { label: labelOf(r.id) }), el);
      return false;
    }

    // 範圍
    const numVal = Number(val);
    const hasMin = Number.isFinite(r.min);
    const hasMax = Number.isFinite(r.max);

    if (!Number.isNaN(numVal)) {
      if (hasMin && numVal < r.min) {
        markAndFocusInvalid(el);
        alertPopup(
          hasMax ? t('range', { label: labelOf(r.id), min: r.min, max: r.max })
                 : t('minOnly', { label: labelOf(r.id), min: r.min }),
          el
        );
        return false;
      }
      if (hasMax && numVal > r.max) {
        markAndFocusInvalid(el);
        alertPopup(
          hasMin ? t('range', { label: labelOf(r.id), min: r.min, max: r.max })
                 : t('maxOnly', { label: labelOf(r.id), max: r.max }),
          el
        );
        return false;
      }
    }

    // OK
    el.classList.remove('is-invalid');
  }

  // 全部通過
  return true;
}

// 讓 onclick/save_sequence() 找得到
window.seq_fastening_check = seq_fastening_check;


</script>