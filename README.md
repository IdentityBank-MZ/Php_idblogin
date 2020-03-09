# PHP project - Login for IDB portals

[Identity Bank](https://www.identitybank.eu)

### idblogin
We have a separate, unified login for Identity Bank. We have a single login for all our portals giving customers the possibility to use the same login name but different account numbers. That solution gives us an easy way to always create responsive login pages even if the main system will be heavily loaded. Separation adds an extra security layer as the main login system uses an internal firewall to control requests from only specific instances.
