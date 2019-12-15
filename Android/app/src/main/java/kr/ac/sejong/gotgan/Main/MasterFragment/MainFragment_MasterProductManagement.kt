package kr.ac.sejong.gotgan.Main.MasterFragment

import androidx.fragment.app.Fragment
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import kr.ac.sejong.gotgan.R

class MainFragment_MasterProductManagement : Fragment() {

    companion object {

        // 자바에서 정적 메서드(static method)처럼 사용할 수 있도록 함
        @JvmStatic fun getInstance(index : Int) : Fragment {
            // 데이터 주고 받기
            val masterProductManagementFragment = MainFragment_MasterProductManagement()

            val args = Bundle()
            args.putInt("code", index)
            masterProductManagementFragment.arguments = args

            return masterProductManagementFragment
        }
    }

    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?): View? {

        val rootView: ViewGroup = inflater.inflate(R.layout.fragment_main_master_product_management, container, false) as ViewGroup

        return rootView
    }
}