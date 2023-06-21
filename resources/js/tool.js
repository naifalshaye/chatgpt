import Tool from './pages/Tool'
import History from './pages/History'
import View from './pages/View'

Nova.booting((app, store) => {
    Nova.inertia('Chatgpt', Tool);
    Nova.inertia('History', History);
    Nova.inertia('View', View);
})
