package kr.ac.sejong.gotgan.BackEndAPI.UserSystem

import kr.ac.sejong.gotgan.BackEndAPI.*

class BackEndAPI_UserSystemLogin(_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    fun httpsPostSendLogin_UserInfo(_user_id: String, _user_pw: String) : String? {
        data = StringBuffer()
        data!!.append(userIdStr).append(assignment).append(_user_id).append(
            and
        )
        data!!.append(userPwStr).append(assignment).append(_user_pw)
        return super.httpPostDataSendAndReceiveData()
    }

    fun httpsPostSendLogin_UserInfo(_user_id: String, _user_pw: String, _user_uuid: String) : String? {
        data = StringBuffer()
        data!!.append(userIdStr).append(assignment).append(_user_id).append(
            and
        )
        data!!.append(userPwStr).append(assignment).append(_user_pw)

        data!!.append(and)
        data!!.append(userUuidStr).append(assignment).append(_user_uuid)
        return super.httpPostDataSendAndReceiveData()
    }

    fun httpsPostSendLogin_UserInfoWithSession(_session: String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session)
        return super.httpPostDataSendAndReceiveData()
    }

    fun httpsPostSendLogin_UserInfoWithSession(_session: String, _user_uuid: String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session)

        data!!.append(and)
        data!!.append(userUuidStr).append(assignment).append(_user_uuid)
        return super.httpPostDataSendAndReceiveData()
    }

}