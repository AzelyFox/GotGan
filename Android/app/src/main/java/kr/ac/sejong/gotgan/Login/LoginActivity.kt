package kr.ac.sejong.gotgan.Login

import android.app.ProgressDialog
import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import android.os.AsyncTask
import android.os.Bundle
import android.util.Log
import android.widget.Button
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import com.google.android.material.snackbar.Snackbar
import com.google.android.material.textfield.TextInputEditText
import kr.ac.sejong.gotgan.BackEndAPI.UserSystem.BackEndAPI_UserSystemLogin
import kr.ac.sejong.gotgan.Main.MainActivity_Master
import kr.ac.sejong.gotgan.Main.MainActivity_User
import kr.ac.sejong.gotgan.R
import org.json.JSONObject
import java.lang.Exception

class LoginActivity : AppCompatActivity() {

    private var idEdit: TextInputEditText? = null
    private var pwEdit: TextInputEditText? = null
    private var loginBtn: MaterialButton? = null
    private var signUpBtn: Button? = null
    var progressDialog: ProgressDialog? = null
    private var loginExecute: LoginManager? = null
    private var pref : SharedPreferences? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)

            pref = getSharedPreferences("user_info", Context.MODE_PRIVATE)

            val sessionStr = pref!!.getString("session", null)

            if (sessionStr != null) {
                showLoginDialog(
                    getString(R.string.loading_auto_login),
                    getString(R.string.loading_auto_login_message)
                )
                loginExecute = LoginManager()
                loginExecute!!.execute(sessionStr, null, "0", "1", "1", "session")

        }

        initID() // XML과 연결
        loginBtnSetting()
        etcBtnSetting()
    }

    private fun initID() {
        idEdit = findViewById(R.id.id_edit)
        pwEdit = findViewById(R.id.pw_edit)
        loginBtn = findViewById(R.id.login_btn)
        signUpBtn = findViewById(R.id.sign_up_btn)
    }

    private fun etcBtnSetting() {
        signUpBtn?.setOnClickListener {
            startActivity(Intent(this, SignUpActivity::class.java))
        }
    }

    private fun getID(): String = idEdit?.text.toString()
    private fun getPassword(): String = pwEdit?.text.toString()

    private fun showLoginDialog(title : String, message : String) {
        progressDialog = ProgressDialog(this@LoginActivity)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_SPINNER)
        progressDialog!!.setTitle(title)
        progressDialog!!.setMessage(message)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    private fun loginBtnSetting() {
        loginBtn?.setOnClickListener {
            if (idEdit!!.length() == 0 && pwEdit!!.length() == 0)
                Snackbar.make(it, R.string.plz_input_id_and_pw, Snackbar.LENGTH_SHORT).show()
            else if (idEdit!!.length() == 0)
                Snackbar.make(it, R.string.plz_input_id, Snackbar.LENGTH_SHORT).show()
            else if (pwEdit!!.length() == 0)
                Snackbar.make(it, R.string.plz_input_pw, Snackbar.LENGTH_SHORT).show()
            else {
                showLoginDialog(getString(R.string.loading_login), getString(R.string.loading_login_message))
                loginExecute = LoginManager()
                loginExecute!!.execute(getID(), getPassword(), "0", "1", "1", "id_pw")
            }
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        try {
            if (loginExecute!!.status == AsyncTask.Status.RUNNING)
                loginExecute!!.cancel(true)
        } catch (e: Exception) {
            e.printStackTrace()
            Log.e("AsyncTaskError", "Message : " + e.message)
        }
    }

    private inner class LoginManager : HttpPostSendLogin() {
        // string to json

        fun getJSONData(): JSONObject {
            return JSONObject(allData)
        }

        fun getData(): String? {
            return allData
        }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("result", getData())
            if (result == 1 && getJSONData()["result"] == 0) {
                var userLevelIntent: Intent? = null
                when (getJSONData()["user_level"]) {
                    0 -> userLevelIntent =
                        Intent(applicationContext, MainActivity_User::class.java)
                    1, 2 -> userLevelIntent =
                        Intent(applicationContext, MainActivity_Master::class.java)
                }
                if (modeName == "id_pw") {
                    val editor : SharedPreferences.Editor = pref!!.edit()
                    editor.putString("session", getJSONData()["session"].toString())
                    editor.apply()
                }
                userLevelIntent!!.putExtra("userAllData", allData)
                startActivity(userLevelIntent)
                this@LoginActivity.finish()
            }
            else {
                val builder = AlertDialog.Builder(this@LoginActivity, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.login_fail)
                when (getJSONData()["result"]) {
                    -1 -> builder.setMessage(R.string.implementation_error)
                    -2 -> builder.setMessage(R.string.server_error)
                    -3 -> builder.setMessage(R.string.wrong_id_pw_error)
                }
                if (modeName == "session") { // session 로그인
                    val editor : SharedPreferences.Editor = pref!!.edit()
                    editor.remove("session")
                    editor.apply()
                    builder.setMessage(R.string.expire_session_error)
                }
                builder.setPositiveButton(android.R.string.ok, null)
                builder.show()
                return
            }
        }
    }

    // execute(id, pw, defaultUseCaches, doInput, doOutput)
    // refactoring 필요
    abstract class HttpPostSendLogin : AsyncTask<String, Int, Int>() {

        private val apiName = "login.php"
        var allData: String? = null
        var modeName : String? = null

        abstract fun onPreTask()
        abstract fun onUpdate(prgInt: Int)
        abstract fun onFinish(result: Int)

        override fun onPreExecute() {
            //super.onPreExecute()
            onPreTask()
        }

        override fun doInBackground(vararg params: String): Int {
            // params[0] == id                      params[0] == session
            // params[1] == pw                      params[1] == null
            // params[2] == defaultUseCaches        params[2] == defaultUseCaches
            // params[3] == doInput                 params[3] == doInput
            // params[4] == doOutput                params[4] == doOutput
            // params[5] == mode(id_pw / session)   params[5] == mode(id_pw / session)
            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[2] == "1") defaultUseCaches = true
            if (params[3] == "1") doInput = true
            if (params[4] == "1") doOutput = true
            modeName = params[5]

            publishProgress(30)

            try {
                val loginAPI = BackEndAPI_UserSystemLogin(
                    apiName,
                    defaultUseCaches,
                    doInput,
                    doOutput
                )
                publishProgress(50)
                if (params[5] == "id_pw")
                    allData = loginAPI.httpsPostSendLogin_UserInfo(params[0], params[1])
                else if (params[5] == "session")
                    allData = loginAPI.httpsPostSendLogin_UserInfoWithSession(params[0])
                publishProgress(100)
            } catch (e: Exception) {
                Log.e("HttpPostSendLogin Error", "Message : " + e.message)
                Log.e("HttpPostSendLogin Error", "LocalizedMessage : " + e.localizedMessage)
                return 0
            }
            return 1
        }

        override fun onProgressUpdate(vararg values: Int?) {
            //super.onProgressUpdate(*values)
            onUpdate(values[0]!!)
        }

        override fun onPostExecute(result: Int?) {
            super.onPostExecute(result)
            onFinish(result!!)
        }

    }

    /*
    private fun httpPostDataSend(url : String) : String? {
        try {

            val _url: URL = URL(url)
            var http: HttpsURLConnection = _url.openConnection() as HttpsURLConnection
            http.defaultUseCaches = false
            http.doInput = true // 서버에서 읽기 모드
            http.doOutput = true // 서버에서 쓰기 모드
            http.requestMethod = "POST" // POST 전송 방식 설정

            var buffer : StringBuffer = StringBuffer()
            buffer.append("user_id").append("=").append(getID()).append("&");
            buffer.append("user_pw").append("=").append(getPassword())

            var strParams : String = buffer.toString()
            var os : OutputStream = http.outputStream
            os.write(strParams.toByteArray())
            os.flush()
            os.close()

            if (http.responseCode != HttpURLConnection.HTTP_OK)
                return null

            val reader : BufferedReader = BufferedReader(InputStreamReader(http.inputStream, "UTF-8"))

            var line : String? = reader.readLine()
            var page : String = "";

            while (line != null) {
                page += line
                line = reader.readLine()
            }

            return page
        }
        catch (e : Exception) {
            e.printStackTrace()
        }
        return null
    }
     */

}