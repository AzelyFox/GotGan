//
//  TabViewController.swift
//  Gotgan_iOS
//
//  Created by Gon on 2019/11/15.
//  Copyright © 2019 reqon. All rights reserved.
//

import SwiftUI

struct AppView: View {
    var body: some View {
        TabView{
            HomeView().tabItem
            {
                Image("baseline_home_black_18dp")
                Text("홈")
            }.tag(0)
            
            HistoryView().tabItem
            {
                Image("baseline_history_black_18dp")
                Text("히스토리")
            }.tag(1)
        }
    }
}

struct TabViewController_Previews: PreviewProvider {
    static var previews: some View {
        TabViewController()
    }
}
