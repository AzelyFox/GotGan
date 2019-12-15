package kr.ac.sejong.gotgan.BackEndAPI.ProductSystem

import kr.ac.sejong.gotgan.BackEndAPI.*

class BackEndAPI_ProductSystemProductGroupAdd (_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    // assignment == "="

    fun httpsPostSend_ProductGroupAdd(_session : String, _productGroupName : String, _productGroupRentable : Int) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        data!!.append(productGroupNameStr).append(assignment).append(_productGroupName).append(and)
        data!!.append(productGroupRentableStr).append(assignment).append(_productGroupRentable)
        return super.httpPostDataSendAndReceiveData()
    }

    fun httpsPostSend_ProductGroupAdd(_session : String, _productGroupName : String, _productGroupRentable : Int, _productGroupPriority : Int) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        data!!.append(productGroupNameStr).append(assignment).append(_productGroupName).append(and)
        data!!.append(productGroupRentableStr).append(assignment).append(_productGroupRentable).append(and)
        data!!.append(productGroupPriorityStr).append(assignment).append(_productGroupPriority)
        return super.httpPostDataSendAndReceiveData()
    }
}