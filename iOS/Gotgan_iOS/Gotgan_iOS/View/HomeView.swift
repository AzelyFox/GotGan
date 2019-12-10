//
//  HomeView.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/15.
//  Copyright © 2019 reqon. All rights reserved.
//

import SwiftUI
let width = UIScreen.main.bounds.width
let height = UIScreen.main.bounds.height
let scale = UIScreen.main.scale

struct HomeView: View {
    var body: some View {
        VStack
            {
                HStack
                {
                    Text("Home")
                    .foregroundColor(Color.white)
                }
                .frame(minWidth: 0, maxWidth: .infinity, minHeight: 0, maxHeight: 50,alignment: .center)
                .background(Color.orange)

                Spacer()
        }.frame(minWidth: 0, maxWidth: .infinity, minHeight: 0, maxHeight: .infinity,alignment: .top)
        
    }
}

struct HomeView_Previews: PreviewProvider {
    static var previews: some View {
        HomeView()
    }
}
