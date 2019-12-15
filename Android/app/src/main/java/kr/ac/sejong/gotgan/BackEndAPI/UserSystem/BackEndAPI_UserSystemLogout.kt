package kr.ac.sejong.gotgan.BackEndAPI.UserSystem

import kr.ac.sejong.gotgan.BackEndAPI.BackEnd_API
import kr.ac.sejong.gotgan.BackEndAPI.assignment
import kr.ac.sejong.gotgan.BackEndAPI.sessionStr

class BackEndAPI_UserSystemLogout(
    _apiName: String,
    _defaultUseCaches: Boolean,
    _doInput: Boolean,
    _doOutput: Boolean
) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    fun httpsPostSendLogout(_session : String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session)
        return super.httpPostDataSendAndReceiveData()
    }
}