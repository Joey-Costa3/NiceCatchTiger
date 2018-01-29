//
//  personalViewController.swift
//  NiceCatch
//
//  Created by Joey Costa on 11/16/15.
//  Copyright © 2015 Joey Costa. All rights reserved.

import Foundation
import UIKit
import CoreData
import Alamofire
import SwiftyJSON

struct MyVariables {
	static var isSubmitted = false
}

/*
Extension resize image help from stack overflow
https://stackoverflow.com/questions/29137488/how-do-i-resize-the-uiimage-to-reduce-upload-image-size
*/
extension UIImage {
	func resized(withPercentage percentage: CGFloat) -> UIImage? {
		let canvasSize = CGSize(width: size.width * percentage, height: size.height * percentage)
		UIGraphicsBeginImageContextWithOptions(canvasSize, false, scale)
		defer { UIGraphicsEndImageContext() }
		draw(in: CGRect(origin: .zero, size: canvasSize))
		return UIGraphicsGetImageFromCurrentImageContext()
	}
	func resized(toWidth width: CGFloat) -> UIImage? {
		let canvasSize = CGSize(width: width, height: CGFloat(ceil(width/size.width * size.height)))
		UIGraphicsBeginImageContextWithOptions(canvasSize, false, scale)
		defer { UIGraphicsEndImageContext() }
		draw(in: CGRect(origin: .zero, size: canvasSize))
		return UIGraphicsGetImageFromCurrentImageContext()
	}
}

class personalViewController: UIViewController, UIPickerViewDataSource, UIPickerViewDelegate, UITextFieldDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate, URLSessionDelegate, URLSessionTaskDelegate, URLSessionDataDelegate, UIGestureRecognizerDelegate {
	
	@IBOutlet weak var activityIndicator: UIActivityIndicatorView!
	//@IBOutlet weak var progressBar: UIProgressView!
	//@IBOutlet weak var progressText: UILabel!
	
	@IBOutlet weak var designTextField: UITextField!
	@IBOutlet weak var nameField: UITextField!
	
	@IBOutlet weak var usernameField: UITextField!
	@IBOutlet weak var phoneNumField: UITextField!
	@IBOutlet weak var designPicker: UIPickerView!
	@IBOutlet var submitButton: UIButton!
	@IBOutlet var uploadButton: UIButton!
	@IBOutlet var takePhoto: UIButton!
	
	var backgroundTouched: UITapGestureRecognizer!
	
	//------------------------ UI METHODS ------------------------
	
	override func viewDidAppear(_ animated: Bool) {
		super.viewDidAppear(animated)
		// Keyboard stuff.
		let center: NotificationCenter = NotificationCenter.default
		center.addObserver(self, selector: #selector(personalViewController.keyboardWillShow), name: NSNotification.Name.UIKeyboardWillShow, object: nil)
		center.addObserver(self, selector: #selector(personalViewController.keyboardWillHide), name: NSNotification.Name.UIKeyboardWillHide, object: nil)
		self.usernameField.text = finalReportData.username
	}
	
	override func viewDidLoad() {
		super.viewDidLoad()
		
		uploadButton.layer.cornerRadius = 5
		takePhoto.layer.cornerRadius = 5
		self.navigationItem.title = ""

		definesPresentationContext = true
		
		photoPicker.delegate = self
		
		designPicker.dataSource = self
		designPicker.delegate = self
		designTextField.isHidden = true
		
		designTextField.delegate = self
		nameField.delegate = self
		usernameField.delegate = self
		phoneNumField.delegate = self
		
		submitButton.isEnabled = true
		//hide progress bar and text
		//progressBar.hidden = true;
		//progressText.hidden = true;
		
		// Keyboard stuff.
//		let center: NSNotificationCenter = NSNotificationCenter.defaultCenter()
//		center.addObserver(self, selector: #selector(personalViewController.keyboardWillShow(_:)), name: UIKeyboardWillShowNotification, object: nil)
//		center.addObserver(self, selector: #selector(personalViewController.keyboardWillHide(_:)), name: UIKeyboardWillHideNotification, object: nil)
		
		//not loaded. load manually
		if(preloadedData.personKinds.count == 0){
			//self.loadPersonKinds()
			preload()
		}
		
		activityIndicator.stopAnimating()
		
		loadUserDefaults()
		backgroundTouched = UITapGestureRecognizer(target: self, action: #selector(locationController.hideKeyboardAndDismissTableViews))
		backgroundTouched.delegate = self
		view.addGestureRecognizer(backgroundTouched!)
	}
	func hideKeyboardAndDismissTableViews(sender: UITapGestureRecognizer)
	{
		self.view.endEditing(true)
		
	}
	override func didReceiveMemoryWarning() {
		super.didReceiveMemoryWarning()
		// Dispose of any resources that can be recreated.
	}
	
	override func viewWillDisappear(_ animated: Bool) {
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name.UIKeyboardWillShow, object: nil)
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name.UIKeyboardWillHide, object: nil)
	}
	

	//Images
	let photoPicker = UIImagePickerController()
	@IBOutlet weak var myImageView: UIImageView!
	

	func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : Any]) {
		let chosenImage = info[UIImagePickerControllerOriginalImage] as! UIImage
		myImageView.contentMode = .scaleAspectFit
		myImageView.image = chosenImage
		finalReportData.image = chosenImage
		dismiss(animated: true, completion: nil)
	}
	func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
		dismiss(animated: true, completion: nil)
	}
	
	@IBAction func takePhotoClicked(sender: AnyObject) {
		if (UIImagePickerController.availableCaptureModes(for: .rear) != nil) {
			photoPicker.sourceType = .camera
			present(photoPicker, animated: true, completion: nil)
		} 
	}
	
	@IBAction func uploadPhotoClicked(sender: AnyObject) {
		photoPicker.allowsEditing = false
		photoPicker.sourceType = .photoLibrary
		present(photoPicker, animated: true, completion: nil)
	}
	
	func fixOrientation(img:UIImage) -> UIImage {
		
  if (img.imageOrientation == UIImageOrientation.up) {
	return img;
  }
		
  UIGraphicsBeginImageContextWithOptions(img.size, false, img.scale);
  let rect = CGRect(x: 0, y: 0, width: img.size.width, height: img.size.height)
  img.draw(in: rect)
		
  let normalizedImage : UIImage = UIGraphicsGetImageFromCurrentImageContext()!
  UIGraphicsEndImageContext();
  return normalizedImage;
		
	}
	
	//------------------------ KEYBOARD METHODS ------------------------
	
	func textFieldShouldReturn(_ textField: UITextField) -> Bool {
		self.view.endEditing(true)
		return false
	}
	
	var hasMoved = false
	
	func keyboardWillShow(notification: NSNotification) {
		let info:NSDictionary = notification.userInfo! as NSDictionary
		let keyboardSize = (info[UIKeyboardFrameBeginUserInfoKey] as! NSValue).cgRectValue
		
		let keyboardHeight: CGFloat = keyboardSize.height
		
		if !hasMoved { //&& phoneNumField.isFirstResponder() {
			self.view.center.y = self.view.center.y - keyboardHeight + CGFloat(50)
			hasMoved = true
		}
	}
	
	func keyboardWillHide(notification: NSNotification) {
		let info: NSDictionary = notification.userInfo! as NSDictionary
		let keyboardSize = (info[UIKeyboardFrameBeginUserInfoKey] as! NSValue).cgRectValue
		
		let keyboardHeight: CGFloat = keyboardSize.height
		
		if hasMoved { //&& phoneNumField.isFirstResponder() {
			self.view.center.y = self.view.center.y + keyboardHeight - CGFloat(50)
			hasMoved = false
		}
	}
	
	
	func numberOfComponents(in pickerView: UIPickerView) -> Int {
		return 1
	}
	func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
		return preloadedData.personKinds.count
	}
	
	//MARK: Delegates
	func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String? {
		return preloadedData.personKinds[row]
	}
	
	var designSelection: String = ""
	
	func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
		if preloadedData.personKinds[row] == "Other" {
			designTextField.isHidden = false;
		} else {
			designTextField.isHidden = true;
		}
		designSelection = preloadedData.personKinds[row]
		print("You have selected \(designSelection)")
	}
	
	func pickerView(_ pickerView: UIPickerView, viewForRow row: Int, forComponent component: Int, reusing view: UIView?) -> UIView {
		let pickerLabel = UILabel()
		let titleData = preloadedData.personKinds[row]
		if (self.view.traitCollection.horizontalSizeClass == UIUserInterfaceSizeClass.regular) {
			let myTitle = NSAttributedString(string: titleData, attributes: [NSFontAttributeName:UIFont(name: "Helvetica", size: 36.0)!,NSForegroundColorAttributeName:UIColor.black])
			pickerLabel.attributedText = myTitle
			pickerLabel.textAlignment = .center
			return pickerLabel
		} else {
			let myTitle = NSAttributedString(string: titleData, attributes: [NSFontAttributeName:UIFont(name: "Helvetica", size: 24.0)!,NSForegroundColorAttributeName:UIColor.black])
			pickerLabel.attributedText = myTitle
			pickerLabel.textAlignment = .center
			return pickerLabel
		}
	}
	
	func pickerView(_ pickerView: UIPickerView, rowHeightForComponent component: Int) -> CGFloat {
		if (self.view.traitCollection.horizontalSizeClass == UIUserInterfaceSizeClass.regular) {
			return 36.0
		} else {
			return 30.0
		}
	}
	
	func addPersonalInfo() {
		if designSelection == "Other" {
			finalReportData.designation = designTextField.text!
		} else if designSelection == "" {
			finalReportData.designation = "Faculty"
		} else {
			finalReportData.designation = designSelection
		}
		finalReportData.name = nameField.text!
		finalReportData.username = usernameField.text!
		finalReportData.phoneNum = phoneNumField.text!
	}
	
	func resignResponders(){
		nameField.resignFirstResponder()
		usernameField.resignFirstResponder()
		phoneNumField.resignFirstResponder()
		
	}
	//------------------------ ACTION HANDLERS ------------------------
	
	//This submits the form and calls method to save to DB
	@IBAction func submitClicked(sender: AnyObject) {
		MyVariables.isSubmitted = true
		self.saveUserDefaults()
		self.addPersonalInfo()
		//self.addToDatabase()
		if fieldsAreValid()
		{
		self.resignResponders()
		submitButton.isEnabled = false
		self.submitReport() //test
		}
		
	}
	
	//------------------------ REMOTE DB METHODS ------------------------
	func submitReport(){
		let session = URLSession.shared
		let myUrl:String = baseURL + "/models/submitReport.php"
		let url:NSURL = NSURL(string: myUrl)!
		
		let request = NSMutableURLRequest(url:url as URL)
		request.httpMethod="POST"
		request.cachePolicy = NSURLRequest.CachePolicy.reloadIgnoringCacheData
		
		print ("DEPT: \(finalReportData.departmentName)")
		//PARAMETERS
		let isIOS = 1;
		let paramString = "description=\(finalReportData.incidentDesc)" +
			"&involvementKind=\(finalReportData.involveKind)" +
			"&reportKind=\(finalReportData.reportKind)" +
			"&buildingName=\(finalReportData.buildingName)" +
			"&room=\(finalReportData.roomNum)" +
			"&personKind=\(finalReportData.designation)" +
			"&name=\(finalReportData.name)" +
			"&username=\(finalReportData.username)" +
			"&phone=\(finalReportData.phoneNum)" +
			"&department=\(finalReportData.departmentName)" +
			"&reportTime=\(self.getDateTimeString())" +
			"&statusID=1" +
			"&actionTaken=" +
			"&incidentTime=\(finalReportData.time)" +
			"&isIOS=\(isIOS)"
		request.httpBody = paramString.data(using: String.Encoding.utf8)

		//Actual request task
		let task = session.dataTask(with: request as URLRequest){
			( data, response, error) in

			guard let _:NSData = data as NSData?, let _:URLResponse = response , error == nil else {
				DispatchQueue.main.async(execute: {
					print("NETWORK ERROR")
					print(error!)
					self.myImageView.alpha = 1.0 //make image opaque again
					self.activityIndicator.stopAnimating()
					self.presentNetworkError()
				})
				return
			}
			//no error, process response
			//let dataString = NSString(data: data!, encoding:NSUTF8StringEncoding)
			let json = try! JSON(data: data!)
			
			let jsonData = json["data"]
//			let jsonData = (json as NSDictionary).value(forKey: "data")
			//print("The json['data']")
			//print(jsonData)
			
			if let remoteID = Int(jsonData["id"].stringValue){
				print("remote id: \(remoteID)")
				finalReportData.remoteID = remoteID
				if(self.myImageView.image != nil && self.myImageView.image != UIImage(named: "WhitePaw_NoOutline2", in: Bundle.main, compatibleWith: .none)){
					print("Should upload photo")
					self.uploadPhoto()
				} else {
					DispatchQueue.main.async(execute: {
						print("Uploading with no image...")
						DispatchQueue.main.async {
						self.myImageView.alpha = 1.0
						self.activityIndicator.stopAnimating()
						self.presentThankYou()
						}
					})
				}
			} else {
				DispatchQueue.main.async(execute: {
					print("error getting remote ID")
					DispatchQueue.main.async {
					self.myImageView.alpha = 1.0 //make image opaque again
					self.activityIndicator.stopAnimating()
					self.presentSubmitError()
					}
				})
				return
			}
			
		}
		
		//---- prepare to send data ----
		//animate until uploaded
		activityIndicator.startAnimating()
		
		//there is an image to upload.
		if(myImageView.image != nil){
			DispatchQueue.main.async(execute: {
				self.myImageView.alpha = 0.5
			})
		}
		
		task.resume()
	}
	
	
	
	func uploadPhoto(){
		print("photo upload")
		
		activityIndicator.startAnimating()
		
		let myUrl = NSURL(string: baseURL + "/models/iosUpload.php")
		
		let request = NSMutableURLRequest(url:myUrl! as URL)
		request.httpMethod = "POST"
	
		let param = [
			"description":finalReportData.incidentDesc,
			"involvementKind":finalReportData.involveKind,
			"reportKind":finalReportData.reportKind,
			"buildingName":finalReportData.buildingName,
			"room":finalReportData.roomNum,
			"personKind":finalReportData.designation,
			"name":finalReportData.name,
			"username":finalReportData.username,
			"phone":finalReportData.phoneNum,
			"department":finalReportData.departmentName,
			"reportTime":self.getDateTimeString(),
			"statusID":"1", //open report id (for all new reports)
			"actionTaken":"",
			"incidentTime":finalReportData.time,
			"imageName" : "\(finalReportData.remoteID)"
		]
		
		let boundary = "Boundary-\(NSUUID().uuidString)"
		
		request.setValue("multipart/form-data; boundary=\(boundary)", forHTTPHeaderField: "Content-Type")
		
		//let imageData = UIImageJPEGRepresentation(finalReportData.image, 1)
		
		let image = fixOrientation(img: finalReportData.image)
		// failed to compress image - return
		guard let smallerImage = image.resized(withPercentage: 0.25) else{
			return
		}
		let imageData = UIImagePNGRepresentation(smallerImage)
		//no image, return
		if(imageData==nil)  { return; }
		
		request.httpBody = createBodyWithParameters(parameters: param, filePathKey: "photo", imageDataKey: imageData! as NSData, boundary: boundary) as Data
		
		let task = URLSession.shared.dataTask(with: request as URLRequest) {
			data, response, error in
			
			print("Task completed")
			if let data = data {

			
				if let jsonResult = NSString(data: data, encoding: String.Encoding.utf8.rawValue){
					//print(jsonResult)
					if(jsonResult.contains("Saved")){
						//print(jsonResult)
						DispatchQueue.main.async {
						self.myImageView.alpha = 1.0
						self.activityIndicator.stopAnimating()
						self.presentThankYou()
						}
					}else{
						print(jsonResult)
						DispatchQueue.main.async {
						
						self.myImageView.alpha = 1.0 //make image opaque again
						self.activityIndicator.stopAnimating()
						self.presentSubmitError()
						}
					}
					
				}else{
					DispatchQueue.main.async {
						
						self.myImageView.alpha = 1.0 //make image opaque again
						self.activityIndicator.stopAnimating()
						self.presentSubmitError()
					}
				}
				
			} else if let error = error {
				print(error.localizedDescription)
				DispatchQueue.main.async {
				self.myImageView.alpha = 1.0 //make image opaque again
				self.activityIndicator.stopAnimating()
				self.presentSubmitError()
				}
			}
		}
		task.resume()
	}//func
	
	//to show progress
	/*
	func URLSession(session: NSURLSession, task: NSURLSessionTask, didSendBodyData bytesSent: Int64, totalBytesSent: Int64, totalBytesExpectedToSend: Int64)
	{
	print("didSendBodyData")
	let uploadProgress:Float = Float(totalBytesSent) / Float(totalBytesExpectedToSend)
	
	//progressBar.progress = uploadProgress
	let progressPercent = Int(uploadProgress*100)
	//progressText.text = "\(progressPercent)%"
	print(uploadProgress)
	}*/
	
	//------------------------ HELPER METHODS ------------------------
	
	//popup window that appears after submission
	func presentThankYou() {
		let alert = UIAlertController(title: "Thank You", message: "Thank you for submitting a Nice Catch report. The Office of Research Safety will review your report.", preferredStyle: UIAlertControllerStyle.alert)
		alert.addAction(UIAlertAction(title: "Ok", style: UIAlertActionStyle.default, handler: { (action: UIAlertAction!) in
			let _ = self.navigationController?.popToRootViewController(animated: true)
		}))
		self.present(alert, animated: true, completion: nil)
	}
	
	func presentNetworkError(){
		let alert = UIAlertController(title: "Whoops!", message: "There was a network error. Please try submitting again.", preferredStyle: UIAlertControllerStyle.alert)
		alert.addAction(UIAlertAction(title: "Ok", style: UIAlertActionStyle.default, handler:
			{
				action in
				self.submitButton.isEnabled = true
		}))
		self.present(alert, animated: true, completion: nil)
	}
	
	func presentSubmitError(){
		let alert = UIAlertController(title: "Whoops!", message: "There was an error submitting your report. Please try submitting again.", preferredStyle: UIAlertControllerStyle.alert)
		alert.addAction(UIAlertAction(title: "Ok", style: UIAlertActionStyle.default, handler:
			{
				action in
				self.submitButton.isEnabled = true
		}))
		self.present(alert, animated: true, completion: nil)
	}
	func presentEmptyFieldsError(){
		let alert = UIAlertController(title: "Invalid Input", message: "Please enter a name and your Clemson username", preferredStyle: UIAlertControllerStyle.alert)
		alert.addAction(UIAlertAction(title: "Ok", style: UIAlertActionStyle.default, handler: nil))
		self.present(alert, animated: true, completion: nil)
	}
	
	
	func createBodyWithParameters(parameters: [String: String]?, filePathKey: String?, imageDataKey: NSData, boundary: String) -> NSData {
		let body = NSMutableData();
		
		if parameters != nil {
			for (key, value) in parameters! {
				body.appendString(string: "--\(boundary)\r\n")
				body.appendString(string: "Content-Disposition: form-data; name=\"\(key)\"\r\n\r\n")
				body.appendString(string: "\(value)\r\n")
			}
		}
		
		let filename = "report-image.png"
		
		let mimetype = "image/png"
		//print("IMAGE DATA KEY: " + "\(imageDataKey)")
		body.appendString(string: "--\(boundary)\r\n")
		body.appendString(string: "Content-Disposition: form-data; name=\"\(filePathKey!)\"; filename=\"\(filename)\"\r\n")
		body.appendString(string: "Content-Type: \(mimetype)\r\n\r\n")
		body.append(imageDataKey as Data)
		body.appendString(string: "\r\n")
		
		body.appendString(string: "--\(boundary)--\r\n")
		
//		print("body")
//		print(body)
//		
		return body
	}//func
	// return the current date time
	func getDateTimeString() -> String{
		let now = NSDate()
		
		let dayTimePeriodFormatter = DateFormatter()
		dayTimePeriodFormatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
		
		return dayTimePeriodFormatter.string(from: now as Date)
	}
	
	
	
	//---------------- VALIDATION ----------------
	func fieldsAreValid() -> Bool {
		if ((designSelection == "Other" && (designTextField.text == "" || (designTextField.text?.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)!)) || ((nameField.text == "" || (nameField.text?.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)!) || (usernameField.text == "" || (usernameField.text?.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)!))){
			if(nameField.text == "" || usernameField.text == "")
			{
				presentEmptyFieldsError()
				return false
			}
			return false
		} else {
			return true
		}
	}
	
	//determine whether to block segue or not
	
	override func shouldPerformSegue(withIdentifier identifier: String, sender: Any?) -> Bool {
		return fieldsAreValid()
	}
	
	func loadUserDefaults(){
		let defaults = UserDefaults.standard
		if let defaultFullName = defaults.string(forKey: "reportUserFullName"){
			nameField.text = defaultFullName
		}
		if let defaultUserUsername = defaults.string(forKey: "reportUserUsername"){
			usernameField.text = defaultUserUsername
		}
		if let defaultUserPhone = defaults.string(forKey: "reportUserPhone"){
			phoneNumField.text = defaultUserPhone
		}
		if let defaultUserDesignationPicker = defaults.string(forKey: "reportUserDesignationPicker"){
			print("The data saved in the user defaults for picker view is \(defaultUserDesignationPicker)")
			designSelection = defaultUserDesignationPicker
			if let pickerIndex = preloadedData.personKinds.index(of: defaultUserDesignationPicker){
				designPicker.selectRow(pickerIndex, inComponent: 0, animated: true)
				print("Setting the picker index to \(pickerIndex)")
			}
			else {
				print("Failed setting picker index")
			}
		}
		else{
			print("Failed getting picker index")
		}
		if let defaultUserDesignationTextField = defaults.string(forKey: "reportUserDesignationTextField"){
			designTextField.text = defaultUserDesignationTextField
		}
	}
	
	func saveUserDefaults(){
		let defaults = UserDefaults.standard
		defaults.set(nameField.text, forKey: "reportUserFullName")
		defaults.set(usernameField.text, forKey: "reportUserUsername")
		defaults.set(phoneNumField.text, forKey: "reportUserPhone")
		print("Saving the default picker to the selected: \(designSelection)")
		defaults.set(designSelection, forKey: "reportUserDesignationPicker")
		defaults.set(designTextField.text, forKey: "reportUserDesignationTextField")
	}
}

extension NSMutableData {
	
	func appendString(string: String) {
		let data = string.data(using: String.Encoding.utf8, allowLossyConversion: true)
		append(data!)
	}
}
