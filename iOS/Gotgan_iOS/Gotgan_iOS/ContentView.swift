//
//  ContentView.swift
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
            Text("Gotgan").font(.largeTitle)
                .fontWeight(.bold)
                .foregroundColor(Color.orange)
                .padding(20)
                .frame(width: 200.0, height: 100.0)
            
            Group
            {
                TextField("ID",text:$user_id)
                SecureField("Password",text:$user_pw)
            }.frame(width:200,height:30)
                .padding(10)
                .border(Color.black,width: 2)
            
            Spacer().frame(height: 20)
            
            Button(action:{
                // request login
                print("ID : \(self.user_id) PW : \(self.user_pw)")
                NetworkManager.Instance.RequestLogin(id: self.user_id, pw: self.user_pw)
            }){
                Text("Sign In").fontWeight(.bold)
                    .frame(width: 100,height: 45)
                    .foregroundColor(Color.white)
                    .background(Color.orange)
            }
            
            Button(action:{
                // add user
            }){
                Text("Sign Up").fontWeight(.bold)
                    .frame(width: 100,height: 45)
                    .foregroundColor(Color.white)
                    .background(Color.orange)
            }
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
