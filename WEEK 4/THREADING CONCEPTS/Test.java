package gym.booking.client;

import gym.booking.system.SlotBookingThread;
import gym.booking.system.Bookslot;

public class Test {

    public static void main(String[] args) {
        Bookslot Bookslot = new Bookslot();
        SlotBookingThread t1 = new SlotBookingThread(Bookslot,"John",2);
        SlotBookingThread t2 = new SlotBookingThread(Bookslot,"Martin",2);
        
        t1.start();
        t2.start();
    }
}
