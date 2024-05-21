#define _CRT_SECURE_NO_WARNINGS

#include <iostream>
#include <chrono>
#include <future>
#include <thread>

#if defined(WIN32) || defined(_WIN32)
#include <conio.h>
#include <Windows.h>
#else
#include <cstdlib>
#include <csignal>
#include <sstream>
#endif

using namespace std;


#if defined(WIN32) || defined(_WIN32)
#define itoa _itoa
#define exit _exit
#else
#define itoa(i, s, dummy) { std::stringstream ss; ss << i; s = (char*)ss.str().c_str(); }

#endif

#define null NULL

time_t WDT_RESET = 0;
time_t WDT_TARGET = 60;

char ReadSTDIn() {
    char z;
    cin >> z;
    return z;
}

bool doOutput = true;
void sensMsg(string test) {
    if (doOutput) {
        cout << test << endl;
    }
}
void sensMsg(string test, int f) {
    char* _int = new char[100];
    itoa(f, _int, 10);
    string z;
    z += test;
    z += _int;
    sensMsg(z);
}
void sensMsg(string test, char* f) {
    string z;
    z += test;
    z += (char*)f;
    sensMsg(z);
}
void sensMsg(string test, int f, string test2) {
    char* _int = new char[100];
    itoa(f, _int, 10);
    string z;
    z += test;
    z += _int;
    z += test2;
    sensMsg(z);
}

int main(int argc, char** argv)
{
    if (argc < 2) {
        sensMsg("Error: PID needed as first argument!");
        return 1;
    }

    int PID = atoi(argv[1]);

    if (PID <= 0) {
        sensMsg("Error: PID must be positive INT value! You sent: ", argv[1]);
        return 1;
    }

    int targetMod;
    if (argc > 2 && (targetMod = atoi(argv[2])) > 0) {
        WDT_TARGET = targetMod;
    }

    for (int i = 1; i < argc; i++) {
        string arg = argv[i];
        if (arg == "-s" || arg == "--silent") {
            doOutput = false;
        }
    }

    sensMsg("Watchdog Reset Target Time: ", WDT_TARGET, "s");

    WDT_RESET = time(null) + WDT_TARGET;

    std::future<char> future = std::async(ReadSTDIn);

    bool doKill = false;
    while (!doKill) {
        char RST = 0;

        if (future.wait_for(std::chrono::seconds(0)) == std::future_status::ready) {
            RST = (char)future.get();
            future = std::async(ReadSTDIn);
        }

        time_t now = time(null);
        if (!RST) {
            if (WDT_RESET < now) {
                doKill = true;
            }
        } else {
            sensMsg("Watchdog has been reset on ", WDT_RESET - now, "s!");
            WDT_RESET = time(null) + WDT_TARGET;
        }
    }

    sensMsg("Watchdog was not reset in time, killing all!");
    
#if defined(WIN32) || defined(_WIN32)
    HANDLE handle = OpenProcess(PROCESS_TERMINATE, false, PID);
    if (NULL != handle) {
        TerminateProcess(handle, 0);
        CloseHandle(handle);
    }
#else
    kill(PID, SIGKILL);
#endif

    exit(0);
}