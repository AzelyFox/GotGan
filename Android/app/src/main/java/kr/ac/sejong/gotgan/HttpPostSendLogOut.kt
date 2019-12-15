package kr.ac.sejong.gotgan

import android.os.AsyncTask
import android.util.Log
import kr.ac.sejong.gotgan.BackEndAPI.UserSystem.BackEndAPI_UserSystemLogout

// execute(id, pw, defaultUseCaches, doInput, doOutput)
// refactoring 필요
abstract class HttpPostSendLogOut : AsyncTask<String, Int, Int>() {

    private val apiName = "logout.php"
    protected var allData: String? = null

    abstract fun onPreTask()
    abstract fun onUpdate(prgInt: Int)
    abstract fun onFinish(result: Int)

    override fun onPreExecute() {
        //super.onPreExecute()
        onPreTask()
    }

    override fun doInBackground(vararg params: String): Int {
        // params[0] == session
        // params[1] == defaultUseCaches
        // params[2] == doInput
        // params[3] == doOutput
        var defaultUseCaches = false
        var doInput = false
        var doOutput = false
        if (params[1] == "1") defaultUseCaches = true
        if (params[2] == "1") doInput = true
        if (params[3] == "1") doOutput = true

        publishProgress(30)

        try {
            val loginAPI = BackEndAPI_UserSystemLogout(apiName,defaultUseCaches,doInput,doOutput)
            publishProgress(70)
            allData = loginAPI.httpsPostSendLogout(params[0])
            publishProgress(100)
        } catch (e: Exception) {
            Log.e("LogOut Error", "Message : " + e.message)
            Log.e("LogOut Error", "LocalizedMessage : " + e.localizedMessage)
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