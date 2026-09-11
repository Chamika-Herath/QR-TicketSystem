import PublicEventController from './PublicEventController'
import DashboardController from './DashboardController'
import CheckInController from './CheckInController'
import AdminEventController from './AdminEventController'
import UserController from './UserController'
import Settings from './Settings'

const Controllers = {
    PublicEventController: Object.assign(PublicEventController, PublicEventController),
    DashboardController: Object.assign(DashboardController, DashboardController),
    CheckInController: Object.assign(CheckInController, CheckInController),
    AdminEventController: Object.assign(AdminEventController, AdminEventController),
    UserController: Object.assign(UserController, UserController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers