package kr.ac.sejong.gotgan.Login

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import com.google.android.material.snackbar.Snackbar
import com.google.android.material.textfield.TextInputEditText
import kr.ac.sejong.gotgan.R

class SignUpActivity : AppCompatActivity() {

    private var idEdit: TextInputEditText? = null
    private var pwEdit: TextInputEditText? = null
    private var nameEdit: TextInputEditText? = null
    private var schoolIDNumEdit: TextInputEditText? = null
    private var emailEdit: TextInputEditText? = null
    private var phoneNumberEdit: TextInputEditText? = null
    private var signUpBtn: MaterialButton? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_signup)

        initID()
        signUp()

    }

    private fun initID() {
        idEdit = findViewById(R.id.id_edit)
        pwEdit = findViewById(R.id.pw_edit)
        nameEdit = findViewById(R.id.name_edit)
        schoolIDNumEdit = findViewById(R.id.student_id_number_edit)
        emailEdit = findViewById(R.id.email_edit)
        phoneNumberEdit = findViewById(R.id.phone_number_edit)
        signUpBtn = findViewById(R.id.sign_up_btn)
    }

    private fun editTextLengthCheck(): String {
        var message = ""
        if (idEdit?.length() == 0)
            message += "아이디"
        if (pwEdit?.length() == 0)
            message += ", 비밀번호"
        if (nameEdit?.length() == 0)
            message += ", 이름"
        if (schoolIDNumEdit?.length() == 0)
            message += ", 학번"
        if (emailEdit?.length() == 0)
            message += ", 이메일"
        if (phoneNumberEdit?.length() == 0)
            message += ", 휴대폰 번호"

        if (message.isEmpty())
            return message

        if (message[message.length - 1] == '디' || message[message.length - 1] == '호')
            message += "를 입력해주세요 :)"
        else
            message += "을 입력해주세요 :)"

        return message
    }

    private fun signUp() {
        signUpBtn?.setOnClickListener {
            val snackBarMessage : String = editTextLengthCheck()
            if (snackBarMessage.isNotEmpty())
                Snackbar.make(it, snackBarMessage, Snackbar.LENGTH_SHORT).show()
            else {
                // 통신
            }
        }
    }

    

}