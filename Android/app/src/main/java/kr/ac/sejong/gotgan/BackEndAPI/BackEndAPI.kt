package kr.ac.sejong.gotgan.BackEndAPI

import android.util.Log
import java.io.BufferedReader
import java.io.InputStreamReader
import java.io.OutputStream
import java.net.URL
import javax.net.ssl.HttpsURLConnection

var https : HttpsURLConnection? = null

open class BackEnd_API {

    var data : StringBuffer? = null
    var url : URL? = null

    protected fun httpsPostConnect(apiName : String) {
        url = URL("https://api.devx.kr/GotGan/v1/$apiName")
        https = url!!.openConnection() as HttpsURLConnection
    }

    protected fun httpsPostSetting(_defaultUseCaches : Boolean, _doInput : Boolean, _doOutput : Boolean) {
        https!!.defaultUseCaches = _defaultUseCaches
        https!!.doInput = _doInput
        https!!.doOutput = _doOutput
        https!!.requestMethod = "POST"
    }

    protected fun httpPostDataSendAndReceiveData() : String? {
        // 서버로 전송할 데이터가 없는 경우 함수를 그냥 종료함 - 즉, 각 API에 맞는 메소드 호출 필요(현재 클래스를 상속받는 자식 클래스에 있는 메소드)
        if (data == null || data.toString() == "") {
            Log.e("Data Null Error", "First, call the child's method !!")
            return null
        }

        // 데이터 전송 준비
        val strParams : String = data.toString()

        // 데이터 전송
        var os : OutputStream = https!!.outputStream
        os.write(strParams.toByteArray())
        os.flush()
        os.close()

        // 통신 에러 체크
        if (https!!.responseCode != HttpsURLConnection.HTTP_OK)
            return null

        // 데이터 받아오기 준비
        val reader = BufferedReader(InputStreamReader(https!!.inputStream, "UTF-8"))

        var line : String? = reader.readLine()
        var page = "";

        while (line != null) {
            page += line
            line = reader.readLine()
        }

        return page
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