//
//  finalReportData.swift
//  NiceCatch
//
//  Created by Joey Costa on 11/22/15.
//  Copyright © 2015 Joey Costa. All rights reserved.

import Foundation
import UIKit
import Alamofire

let baseURL = "https://nicecatchtiger.com"
struct finalReportData {
    static var reportKind: String = ""
    static var involveKind: String = ""
    static var incidentDesc: String = ""
    static var image: UIImage = UIImage()
    static var departmentName: String = ""
    static var buildingName: String = ""
    static var roomNum: String = ""
    static var time: String = ""
    static var designation: String = ""
    static var name: String = ""
    static var username: String = ""
    static var phoneNum: String = ""
    static var remoteID: Int = -1
}

struct preloadedData {
    static var involvementKinds: [String] = []
    static var reportKinds: [String] = []
    static var buildingNames: [String] = []
    static var departmentNames: [String] = []
    static var personKinds: [String] = []
}


func clearData() {
	print("Clearing data")
    finalReportData.reportKind = ""
    finalReportData.involveKind = ""
    finalReportData.incidentDesc = ""
    finalReportData.image = UIImage(named: "WhitePaw_NoOutline2", in: Bundle.main, compatibleWith: .none)!
    finalReportData.departmentName = ""
    finalReportData.buildingName = ""
    finalReportData.roomNum = ""
    finalReportData.time = ""
    finalReportData.designation = ""
    finalReportData.name = ""
    finalReportData.username = ""
    finalReportData.phoneNum = ""
    
    preloadedData.involvementKinds = []
    preloadedData.reportKinds = []
    preloadedData.buildingNames = []
    preloadedData.departmentNames = []
    preloadedData.personKinds = []
}

func preload(){
    //-------- LOAD INVOLVEMENT NAMES FROM DB --------
	Alamofire.request(baseURL + "/api/v1/involvements").responseJSON { response in
        if let JSON = response.result.value as? NSDictionary{
//			print(JSON)
//            let jsonArray = JSON["data"] as? NSMutableArray
			
			let jsonArray = JSON.value(forKey: "data") as? [AnyObject]
//			let jsonArray2 = JSON[8]
////			print(array)
//			print(jsonArray2)
            if(jsonArray != nil){
                for item in jsonArray! {
                    let string = (item as AnyObject)["involvementKind"]!
//					print(string)
                    preloadedData.involvementKinds.append(string! as! String)
                }
				NotificationCenter.default.post(name: NSNotification.Name(rawValue: "involvementKindSuccess"), object: nil)
            }
			else{
				print("jsonArray is nil")
			}
            print("involvementKinds array is \(preloadedData.involvementKinds)")
        }
    }
	
    //-------- LOAD REPORT KINDS FROM DB --------
    Alamofire.request(baseURL + "/api/v1/reportKinds/get").responseJSON { response in
		if let JSON : NSDictionary = response.result.value as? NSDictionary {

//            let jsonArray = JSON["data"] as? NSMutableArray
			let jsonArray = JSON.value(forKey: "data") as? [AnyObject]
            if(jsonArray != nil){
				for item in jsonArray! {
					//print((item as AnyObject)["reportKind"]!)
					let string = (item as AnyObject)["reportKind"]!
					preloadedData.reportKinds.append(string! as! String)
                }
				NotificationCenter.default.post(name: NSNotification.Name(rawValue: "reportKindSuccess"), object: nil)
            }
            print("reportKinds array is \(preloadedData.reportKinds)")
        }
    }
    
    //------------------------ LOAD BUILDING NAMES FROM DB ------------------------
    Alamofire.request(baseURL + "/api/v1/buildings").responseJSON { response in
		if let JSON = response.result.value as? NSDictionary{
//			let jsonArray = JSON["data"] as? NSMutableArray
			let jsonArray = JSON.value(forKey: "data") as? [AnyObject]

            if(jsonArray != nil){
                for item in jsonArray! {
                    let string = (item as AnyObject)["buildingName"]!
                    preloadedData.buildingNames.append(string! as! String)
                }
				NotificationCenter.default.post(name: NSNotification.Name(rawValue: "buildingsSuccess"), object: nil)
            }
            print("buildings array is \(preloadedData.buildingNames)")
        }
    }
    
    //------------------------ LOAD DEPARTMENT NAMES FROM DB ------------------------
    Alamofire.request(baseURL + "/api/v1/departments").responseJSON { response in
        if let JSON = response.result.value as? NSDictionary{
//            let jsonArray = JSON["data"] as? NSMutableArray
			let jsonArray = JSON.value(forKey: "data") as? [AnyObject]

            if(jsonArray != nil){
                for item in jsonArray! {
                    let string = (item as AnyObject)["departmentName"]!
                    preloadedData.departmentNames.append(string! as! String)
                }
				NotificationCenter.default.post(name: NSNotification.Name(rawValue: "departmentsSuccess"), object: nil)
            }
            print("departments array is \(preloadedData.departmentNames)")
        }
    }
    
    //------------------------ LOAD DESIGNATION NAMES FROM DB ------------------------
    Alamofire.request(baseURL + "/api/v1/personKinds").responseJSON { response in
        if let JSON = response.result.value as? NSDictionary {
//            let jsonArray = JSON["data"] as? NSMutableArray
			let jsonArray = JSON.value(forKey: "data") as? [AnyObject]
            if(jsonArray != nil){
                for item in jsonArray! {
                    let string = (item as AnyObject)["personKind"]!
                    preloadedData.personKinds.append(string! as! String)
                }
				NotificationCenter.default.post(name: NSNotification.Name(rawValue: "personKindSuccess"), object: nil)
            }
            print("personKinds array is \(preloadedData.personKinds)")
        }
    }
}
