package tol.unicoded;

public class tabler{
        public tabler(){
    }
    
public static int getTable(int what){
    System.out.println("getUniban vagyok="+what);
    if (what>=0xF000) return 15;
    if (what>=0xE000) return 14;
    if (what>=0xD000) return 13;
    if (what>=0xC000) return 12;
    if (what>=0xB000) return 11;
    if (what>=0xA000) return 10;
    if (what>=0x9000) return 9;
    if (what>=0x8000) return 8;
    if (what>=0x7000) return 7;
    if (what>=0x6000) return 6;
    if (what>=0x5000) return 5;
    if (what>=0x4000) return 4;
    if (what>=0x3000) return 3;
    if (what>=0x2000) return 2;
    if (what>=0x1000) return 1;
    if (what>=0x0100) return 0;
    return -1;
    }
}
