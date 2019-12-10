//
//  LoginView.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/04.
//  Copyright © 2019 reqon. All rights reserved.
//

import SwiftUI

struct ContentView: View {
    @State private var user_id: String = ""
    @State private var user_pw: String = ""
    
    var body: some View {
        VStack(alignment: HorizontalAlignment.center,spacing: 15)
        {
            LogoImage().offset(y:50)
            
            /*Text("Gotgan").font(.largeTitle)
                .fontWeight(.bold)
                .foregroundColor(Color.orange)
                .padding(20)
                .frame(width: 200.0, height: 100.0)*/
            
            Group
            {
                TextField("ID",text:$user_id)
                SecureField("Password",text:$user_pw)
            }.frame(width:200,height:30)
                .padding(5)
                .border(Color.black,width: 2)
            
            Button(action:{
                // request login
                print("Login attempt ID : \(self.user_id) PW : \(self.user_pw)")
                NetworkManager.Instance.RequestLogin(id: self.user_id, pw: self.user_pw)
            }){
                Text("Sign In").fontWeight(.light)
                    .frame(width: 100,height: 30)
                    .foregroundColor(Color.black)
                    //.border(Color.black,width:2)
            }
            
            Button(action:{
                print("Register ID : \(self.user_id) PW : \(self.user_pw)")
                NetworkManager.Instance.RequestRegister(id: self.user_id, pw: self.user_pw)
            }){
                Text("Sign Up").fontWeight(.light)
                    .frame(width: 100,height: 30)
                    .foregroundColor(Color.orange)
                    //.background(Color.orange)
            }
            
            /*Button(action:{
                // HTML Test
                NetworkManager.Instance.RequestTest(value: "Qon")
            }){
                Text("HTML Test").fontWeight(.bold)
                    .frame(width: 150,height: 45)
                    .foregroundColor(Color.white)
                    .background(Color.blue)
            }*/
        }
    }
}

#if DEBUG
struct ContentView_Previews: PreviewProvider {
    static var previews: some View {
        ContentView()
    }
}
#endif
