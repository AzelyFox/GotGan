package kr.ac.sejong.gotgan.BackEndAPI.ProductSystem

import kr.ac.sejong.gotgan.BackEndAPI.BackEnd_API
import kr.ac.sejong.gotgan.BackEndAPI.assignment
import kr.ac.sejong.gotgan.BackEndAPI.sessionStr

class BackEndAPI_ProductSystemProductOverview (_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    // assignment == "="

    fun httpsPostSend_ProductOverview(_session : String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session)
        return super.httpPostDataSendAndReceiveData()
    }

}